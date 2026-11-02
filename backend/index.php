<?php

header("Content-Type: application/json");

require_once __DIR__ . "/config/database.php";

$response = [
    "success" => true,
    "message" => "UmaTrainer API is running!",
    "database" => "Connected"
];

echo json_encode($response);

?>