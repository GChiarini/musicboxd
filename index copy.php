<?php
    // Avvio server: php -S 127.0.0.1:5500
    include 'db.php';
    include 'functions.php';

    $albums = getAlbums($conn);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Lista Album</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Album Musicali</h1>
    <a href="add_artist.php">➕ Aggiungi Nuovo Artista</a><br>
    <a href="add_album.php">➕ Aggiungi Nuovo Album</a>

    <div class="albumGrid">
        <?php foreach ($albums as $album): ?>
    <div class="album-card">
        <img src="<?= htmlspecialchars($album['album_cover']) ?>"
             alt="Copertina di <?= htmlspecialchars($album['album_name']) ?> di <?= htmlspecialchars($album['artists']) ?>">
        <div class="album-info">
            <span><?= htmlspecialchars($album['album_name']) ?></span>
            <span><?= htmlspecialchars($album['artists']) ?></span>
            <span><?= htmlspecialchars($album['album_date']) ?></span>
        </div>
    </div>
<?php endforeach; ?>

    </div>
</body>
</html>