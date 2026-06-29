<?php
$host = '127.0.0.1';
$port = 3306;
$dbname = 'freshplate';
$user = 'root';
$pass = '';

$conn = mysqli_connect("$host:$port", $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
