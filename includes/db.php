<?php
$servername = '127.0.0.1';
$username   = 'root';
$password   = 'root';
$dbname     = 'timeline';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Connessione al DB fallita: " . $e->getMessage());
    exit("Errore interno. Contatta l'amministratore.");
}
?>