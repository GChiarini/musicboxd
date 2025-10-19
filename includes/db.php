<?php
$servername = '127.0.0.1';
$username   = 'root';
$password   = 'root';
$dbname     = 'timeline';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    error_log("Connessione al DB fallita: " . $conn->connect_error);
    exit("Errore interno. Contatta l'amministratore.");
}

$conn->set_charset('utf8mb4');
?>
