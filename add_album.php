<?php
require_once 'includes/db.php';
require_once 'includes/functions.php';

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

    if ($album_name === '') {
        exit("Errore: il nome dell'album è obbligatorio.");
    }

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
    addAlbumFull($conn, [
        'name'  => $album_name,
        'date'  => $album_date,
        'genre' => $album_genre,
        'cover' => $album_cover,
        'type'  => $album_type,
        'label' => $album_label
    ], $artist_ids);

    // Redirect alla lista degli album
    header("Location: index.php");
    exit;
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
    </style>
</head>
<body>
<h1>Aggiungi un nuovo album</h1>
<form method="post">
    <input type="text" name="album_name" placeholder="Nome album" required>
    <input type="text" name="artist_name" placeholder="Artisti (separati da ;)">
    <input type="date" name="album_date">
    <input type="text" name="album_genre" placeholder="Genere">
    <input type="text" name="album_cover" placeholder="URL Cover">
    <select name="album_type" required>
        <option value="LP">LP</option>
        <option value="EP">EP</option>
        <option value="Single">Singolo</option>
        <option value="Mixtape">Mixtape</option>
        <option value="Deluxe">Deluxe</option>
        <option value="Compilation">Compilation</option>
    </select>
    <input type="text" name="album_label" placeholder="Etichetta">
    <button type="submit">Salva</button>
</form>
<p><a href="index.php">⬅ Torna alla lista</a></p>
</body>
</html>
