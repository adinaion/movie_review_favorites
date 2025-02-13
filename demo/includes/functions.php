<?php
function runtime_prettier($t)
{
    $h = (int)($t / 60);
    $m = $t % 60;
    if ($h < 1)
        return "$m minutes";
    else 
        if ($h > 1) {
        if ($m != 1)
            return "$h hours $m minutes";
        else
            return "$h hours $m minute";
    } else {
        if ($m != 1)
            return "$h hour $m minutes";
        else
            return "$h hour $m minute";
    }
}



// Funcție pentru conectarea la baza de date
function conectareBazaDeDate() {
    // Parametrii de conectare
    $servername = "localhost";
    $username = "php-user";
    $password = "php-password";
    $dbname = "php-proiect";

    // Crearea conexiunii
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificarea conexiunii
    if ($conn->connect_error) {
        die("Conexiunea la baza de date a eșuat: " . $conn->connect_error);
    }

    // Returnează conexiunea
    return $conn;
}
?>
