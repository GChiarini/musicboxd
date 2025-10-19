<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$error = null;

// Verifica connessione DB
if (!$conn) {
    die("Errore di connessione al database.");
}

// Gestione POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Raccolta e sanificazione dati
    $album_name   = trim($_POST["album_name"] ?? '');
    $album_date   = trim($_POST["album_date"] ?? '');
    $album_genre  = trim($_POST["album_genre"] ?? 'Sconosciuto');
    $album_cover  = trim($_POST["album_cover"] ?? '');
    $album_type   = trim($_POST["album_type"] ?? '');
    $album_label  = trim($_POST["album_label"] ?? '');
    $artist_names = trim($_POST["artist_name"] ?? '');

    // Validazione base
    if ($album_name === '') {
        $error = "Il nome dell'album è obbligatorio.";
    } elseif (!in_array($album_type, ['LP', 'EP', 'Single', 'Mixtape', 'Deluxe', 'Compilation'])) {
        $error = "Tipo di album non valido.";
    } else {
        // Gestione artisti multipli
        $artists = array_filter(array_map('trim', explode(";", $artist_names)));
        $artist_ids = [];

        foreach ($artists as $name) {
            $id = findOrCreateArtist($conn, $name);
            if ($id) {
                $artist_ids[] = $id;
            }
        }

        // Inserimento album e collegamento artisti
        $album_cover = $album_cover ?: 'assets/img/default_cover.jpg';

        $success = addAlbumFull($conn, [
            'name'  => $album_name,
            'date'  => $album_date,
            'genre' => $album_genre,
            'cover' => $album_cover,
            'type'  => $album_type,
            'label' => $album_label
        ], $artist_ids);

        if ($success) {
            header("Location: index.php");
            exit;
        } else {
            $error = "Errore durante il salvataggio dell'album.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Aggiungi Album</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 30px auto; }
        form input, form select, form button { display: block; margin: 10px 0; width: 100%; padding: 8px; }
        button { background: #222; color: white; border: none; cursor: pointer; }
        button:hover { background: #444; }
        .error { color: red; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h1>Aggiungi un nuovo album</h1>

    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="text" name="album_name" placeholder="Nome album" required
               value="<?= htmlspecialchars($_POST['album_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <input type="text" name="artist_name" placeholder="Artisti (separati da ;)"
               value="<?= htmlspecialchars($_POST['artist_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <input type="date" name="album_date"
               value="<?= htmlspecialchars($_POST['album_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <input type="text" name="album_genre" placeholder="Genere"
               value="<?= htmlspecialchars($_POST['album_genre'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <input type="text" name="album_cover" placeholder="URL Cover"
               value="<?= htmlspecialchars($_POST['album_cover'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <select name="album_type" required>
            <?php
            $types = ['LP', 'EP', 'Single', 'Mixtape', 'Deluxe', 'Compilation'];
            $selectedType = $_POST['album_type'] ?? '';
            foreach ($types as $type) {
                $selected = ($type === $selectedType) ? 'selected' : '';
                echo "<option value=\"$type\" $selected>$type</option>";
            }
            ?>
        </select>

        <input type="text" name="album_label" placeholder="Etichetta"
               value="<?= htmlspecialchars($_POST['album_label'] ?? '', ENT_QUOTES, 'UTF-8') ?>">

        <button type="submit">Salva</button>
    </form>

    <p><a href="index.php">⬅ Torna alla lista</a></p>
</body>
</html>
