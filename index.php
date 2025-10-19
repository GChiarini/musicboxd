<?php
// php -S 127.0.0.1:5500
require_once 'includes/db.php';
require_once 'includes/functions.php';

/**
 * Normalizza una stringa:
 * - trim (rimuove spazi ai bordi)
 * - comprime spazi interni multipli in uno
 * - rimuove caratteri di controllo
 * - limita la lunghezza massima
 * - ritorna null se vuota
 */
function normStr(?string $s, int $maxLen = 120): ?string {
    if ($s === null) return null;
    $s = trim($s);
    // comprimi spazi multipli
    $s = preg_replace('/\s+/u', ' ', $s);
    // rimuovi caratteri di controllo ASCII
    $s = preg_replace('/[\x00-\x1F\x7F]/u', '', $s);
    if ($s === '') return null;

    // limita lunghezza per sicurezza/consistenza
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        if (mb_strlen($s, 'UTF-8') > $maxLen) {
            $s = mb_substr($s, 0, $maxLen, 'UTF-8');
        }
    } else {
        if (strlen($s) > $maxLen) {
            $s = substr($s, 0, $maxLen);
        }
    }
    return $s;
}

/**
 * Normalizza l'anno:
 * - deve essere numerico
 * - converte a int
 * - range ragionevole [1900, anno corrente + 1]
 */
function normYear($y): ?int {
    if ($y === null || $y === '') return null;
    $y = is_string($y) ? trim($y) : $y;
    if (!ctype_digit((string)$y)) return null;

    $yy = (int)$y;
    $max = (int)date('Y') + 1; // consenti pre-release
    if ($yy < 1900 || $yy > $max) return null;
    return $yy;
}

// --- Leggi e normalizza i filtri dalla query string ---
$filters = [
    'artist' => normStr($_GET['artist'] ?? null, 80),
    'year'   => normYear($_GET['year'] ?? null),
    'title'  => normStr($_GET['title'] ?? null, 120),
    'type'   => normStr($_GET['type'] ?? null, 40),
];

// Nota: i prepared statements in getAlbums() gestiscono già l'iniezione SQL.
// L'escape per l'HTML va fatto nei template in output (album_card.php, ecc.).

$albums = getAlbums($conn, $filters);

// Se vuoi distinguere errore vs. nessun risultato, puoi controllare $albums === false
$isError = ($albums === false);

include 'includes/header.php';
?>

<main>
    <h1>Album Musicali</h1>
    <?php include 'templates/filter_form.php'; ?>

    <div class="albumGrid">
        <?php if ($isError): ?>
            <p>Errore nel caricamento degli album. Riprova più tardi.</p>
        <?php elseif (!empty($albums)): ?>
            <?php foreach ($albums as $album): ?>
                <?php include 'templates/album_card.php'; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nessun album trovato con questi criteri.</p>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>