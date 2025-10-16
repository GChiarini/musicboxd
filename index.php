<?php
    // Avvio server: php -S 127.0.0.1:5500
    include 'db.php';
    include 'functions.php';

    // Raccogliamo i filtri da GET
    $filters = [
        'artist' => $_GET['artist'] ?? null,
        'year'   => $_GET['year'] ?? null,
        'title'  => $_GET['title'] ?? null,
        'type'  => $_GET['type'] ?? null
    ];

    $albums = getAlbums($conn, $filters);
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Lista Album</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            margin-bottom: 15px;
        }
        a {
            color: #0077cc;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        form {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            background: #f9f9f9;
            border-radius: 8px;
        }
        form label {
            margin-right: 8px;
        }
        form input {
            margin-right: 15px;
            padding: 4px 6px;
        }
        form button {
            padding: 6px 12px;
            border: none;
            background: #0077cc;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
        }
        form button:hover {
            background: #005fa3;
        }
        .albumGrid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .album-card {
            border: 1px solid #ddd;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            background: #fff;
            transition: transform 0.2s ease;
        }
        .album-card:hover {
            transform: scale(1.02);
        }
        .album-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
        }
        .album-info {
            padding: 12px;
        }
        .album-info strong {
            font-size: 1.1em;
            display: block;
        }
        .album-info span {
            display: block;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Album Musicali</h1>
    <a href="add_artist.php">➕ Aggiungi Nuovo Artista</a> | 
    <a href="add_album.php">➕ Aggiungi Nuovo Album</a>

    <!-- FILTRI -->
    <form method="GET">
        <label>Artista:</label>
        <input type="text" name="artist" value="<?= htmlspecialchars($_GET['artist'] ?? '') ?>">

        <label>Anno:</label>
        <input type="number" name="year" value="<?= htmlspecialchars($_GET['year'] ?? '') ?>">

        <label>Titolo:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($_GET['title'] ?? '') ?>">

        <label>Tipo:</label>
        <input type="text" name="type" value="<?= htmlspecialchars($_GET['type'] ?? '') ?>">

        <button type="submit">Filtra</button>
        <a href="index.php">Reset</a>
    </form>

    <!-- GRIGLIA ALBUM -->
    <div class="albumGrid">
        <?php if (!empty($albums)): ?>
            <?php foreach ($albums as $album): ?>
                <div class="album-card">
                    <img src="<?= htmlspecialchars($album['album_cover']) ?>"
                         alt="Copertina di <?= htmlspecialchars($album['album_name']) ?> di <?= htmlspecialchars($album['artists']) ?>">
                    <div class="album-info">
                        <strong>
                            <a href="album.php?id=<?= urlencode($album['album_id']) ?>">
                                <?= htmlspecialchars($album['album_name']) ?>
                            </a>
                        </strong>
                        <span><?= htmlspecialchars($album['artists']) ?></span>
                        <span><?= htmlspecialchars($album['album_date']) ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Nessun album trovato con questi criteri.</p>
        <?php endif; ?>
    </div>
</body>
</html>
