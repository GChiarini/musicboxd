<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

$album_id = $_GET['id'] ?? null;
if (!$album_id || !ctype_digit($album_id)) {
    die("ID album non valido.");
}

$album = getAlbumById($conn, (int)$album_id);
if (!$album) {
    die("Album non trovato.");
}

$songs = getsongsByAlbum($conn, (int)$album_id);
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $song_number  = trim($_POST['song_number'] ?? '');
    $title        = trim($_POST['title'] ?? '');
    $rating       = trim($_POST['rating'] ?? '');
    $tags         = trim($_POST['tags'] ?? '');
    $release_date = trim($_POST['release_date'] ?? '');
    $main_artists = array_filter(array_map('trim', explode(';', $_POST['main_artist'] ?? '')));
    $singers   = array_filter(array_map('trim', explode(';', $_POST['singer'] ?? '')));
    $producers    = array_filter(array_map('trim', explode(';', $_POST['producer'] ?? '')));

    if ($title === '' || empty($main_artists)) {
        $error = "Titolo e artista principale sono obbligatori.";
    } else {
        $song_data = [
            'song_number'  => $song_number,
            'title'         => $title,
            'rating'        => $rating,
            'tags'          => $tags,
            'release_date'  => $release_date
        ];

        $artist_roles = [
            'main'      => $main_artists,
            'singer' => $singers,
            'producer'  => $producers
        ];

        $success = addsong($conn, $album_id, $song_data, $artist_roles);
        if ($success) {
            header("Location: album.php?id=$album_id");
            exit;
        } else {
            $error = "Errore nell'aggiunta della traccia.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($album['album_name']) ?></title>
    <style>
        body { font-family: Arial; max-width: 800px; margin: 30px auto; }
        img { max-width: 200px; }
        .error { color: red; }
        .song-list { margin-top: 30px; }
        .song { border-bottom: 1px solid #ccc; padding: 10px 0; }
        .stars { color: gold; }
    </style>
</head>
<body>
    <h1><?= htmlspecialchars($album['album_name']) ?></h1>
    <p><strong>Artisti:</strong> <?= htmlspecialchars($album['artists']) ?></p>
    <?php if (!empty($album['album_cover'])): ?>
        <img src="<?= htmlspecialchars($album['album_cover']) ?>" alt="Copertina album">
    <?php endif; ?>

    <h2>Aggiungi una traccia</h2>
    <?php if ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post">
        <input type="number" name="song_number" placeholder="Numero traccia" required>
        <input type="text" name="title" placeholder="Titolo" required>
        <input type="number" value=0 step="0.5" min="0" max="5" name="rating" placeholder="Voto (0-5)">
        <input type="text" name="tags" placeholder="Tag separati da ;">
        <input type="date" value="<?= htmlspecialchars($album['album_date'] ?? date('Y-m-d')) ?>" name="release_date">

        <input type="text" name="main_artist" value="<?= htmlspecialchars($album['artists']) ?>" required>
        <input type="text" name="singer" placeholder="singer (separati da ;)">
        <input type="text" name="producer" placeholder="Produttori (separati da ;)">

        <button type="submit">Aggiungi traccia</button>
    </form>

    <div class="song-list">
        <h2>Tracce</h2>
        <?php foreach ($songs as $t): ?>
            <div class="song">
                <strong>#<?= $t['song_number'] ?> - <?= htmlspecialchars($t['title']) ?></strong><br>
                <span class="stars">
                    <?php
                    $full = floor($t['rating']);
                    $half = ($t['rating'] - $full) >= 0.5 ? 1 : 0;
                    echo str_repeat('★', $full);
                    echo $half ? '½' : '';
                    ?>
                </span><br>
                <small>Tag: <?= htmlspecialchars($t['tags']) ?></small><br>
                <small>Data: <?= htmlspecialchars($t['release_date']) ?></small>
            </div>
        <?php endforeach; ?>
    </div>

    <p><a href="index.php">⬅ Torna alla lista</a></p>
</body>
</html>
