<?php
    include 'db.php';
    include 'functions.php';

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $artist_name  = $_POST["artist_name"];
    $real_name = $_POST["real_name"];
    $city    = $_POST["city"];

    // query preparata per sicurezza
    $stmt = $conn->prepare("INSERT INTO artist (artist_name, real_name, city) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $artist_name, $real_name, $city);
    $stmt->execute();

    // redirect alla lista
    header("Location: index.php");
    exit;
    }

?>

<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Aggiungi Artista</title>
</head>
<body>
  <h1>Aggiungi un nuovo artista</h1>
  <form method="post">
    <input type="text" name="artist_name" placeholder="Nome" required><br>
    <input type="text" name="real_name" placeholder="Nome vero"><br>
    <input type="text" name="city" placeholder="Città"><br>
    <button type="submit">Salva</button>
  </form>
  <p><a href="index.php">⬅ Torna alla lista</a></p>
</body>
</html>