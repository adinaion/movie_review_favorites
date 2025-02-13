<?php

// Creează conexiunea
$conn = new mysqli('localhost', 'php-user', 'php-password', 'php-proiect', 10010);

// Verifică dacă există erori la conexiune
if ($conn->connect_error) {
    die("Conexiune eșuată: " . $conn->connect_error);
}

echo "Conexiunea a fost realizată cu succes!";
