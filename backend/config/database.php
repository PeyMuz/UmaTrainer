<?php

$host = "localhost";
$dbname = "umatrainer_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


} catch (PDOException $e) {
    error_log($e->getMessage());
    throw new PDOException("Database connection failed.");
}

?>