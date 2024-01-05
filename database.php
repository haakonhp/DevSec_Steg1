<?php

// Lager variabler til databasen

$host = "localhost";
$dbname = "group_project";
$username = "php_exec";
$password = "%mIg(c{:C[Ns6v0&)$[/NvmJH8[-j:";

// Lag en ny connection
$mysqli = new mysqli(hostname: $host, username: $username, password: $password, database: $dbname);

// Dersom det kommer opp feil, spesifiser med error
if ($mysqli->connect_error) {
    die("Connection error: " . $mysqli->connect_error);
}

return $mysqli;