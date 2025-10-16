<?php
    include 'db.php';
    include 'functions.php';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $album_name  = $_POST["album_name"];
    $artists = explode(";", $_POST["artist_name"]); // separa artisti con ";"
    $album_date    = $_POST["album_date"];
    $album_genre  = /*$_POST["album_genre"]*/"Hip Hop ITA";
    $album_cover = $_POST["album_cover"];
    $album_type    = $_POST["album_type"];
    $album_label    = $_POST["album_label"];
    // query preparata per sicurezza
    $stmt = $conn->prepare("INSERT INTO album (album_name, album_date, album_genre, album_cover, album_type, album_label) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $album_name, $album_date, $album_genre, $album_cover, $album_type, $album_label);
    $stmt->execute();
    $album_id = $stmt->insert_id;
    // per ogni artista
    foreach ($artists as $artist) {
        $artist = trim($artist);
        if ($artist === "") continue;

        // controlla se esiste
        $stmt = $conn->prepare("SELECT id FROM artist WHERE artist_name = ?");
        $stmt->bind_param("s", $artist);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            $artist_id = $row["id"];
        } else {
            // inserisci nuovo artista
            $stmt = $conn->prepare("INSERT INTO artist (artist_name) VALUES (?)");
            $stmt->bind_param("s", $artist);
            $stmt->execute();
            $artist_id = $stmt->insert_id;
        }
        // collega album ↔ artista
        $stmt = $conn->prepare("INSERT INTO album_artist (album_id, artist_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $album_id, $artist_id);
        $stmt->execute();
        
    }

    //redirect alla lista
    header("Location: index.php");
    exit;
    }

?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Aggiungi Album</title>
</head>
<body>
  <h1>Aggiungi un nuovo album</h1>
  <form method="post">
    <input type="text" name="album_name" placeholder="Nome album" required><br>
    <input type="text" name="artist_name" placeholder="Artista"><br>
    <input type="date" name="album_date"><br>
    <input type="text" name="album_genre" placeholder="Genere"><br>
    <input type="text" name="album_cover" placeholder="URL Cover"><br>
    <select name="album_type" id="album_type" required>
        <option value="LP">LP</option>
        <option value="EP">EP</option>
        <option value="Single">Singolo</option>
        <option value="Mixtape">Mixtape</option>
        <option value="Deluxe">Deluxe</option>
        <option value="Compilation">Compilation</option>
    </select><br>
    <input type="text" name="album_label" placeholder="Etichetta"><br>
    <button type="submit">Salva</button>
  </form>
  <p><a href="index.php">⬅ Torna alla lista</a></p>
</body>
</html>