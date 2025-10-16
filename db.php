<?php
$servername = "127.0.0.1";
$username = "root";
$password = "root";
$dbname = "timeline";

// Creazione connessione
$conn = new mysqli($servername, $username, $password, $dbname);

// Controllo connessione
if ($conn->connect_error) {
    die("Connessione fallita: " . $conn->connect_error);
}
?>
