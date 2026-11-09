<?php

header("Content-Type: application/json");

if($_SERVER["REQUEST_METHOD"] !== "PUT"){
   http_response_code(405);

   echo json_encode([
      "success" => false,
      "message" => "method not allowed."
   ]);

   exit;
}

require_once __DIR__ . "/../config/database.php";

$trainer_id = $_GET["id"] ?? null;

if (!$trainer_id || !filter_var($trainer_id)) {
    http_response_code(400);

    echo json_encode([
     "success" => false,
     "message" => "Invaid Trainer ID. Please Try again."
    ]);

    exit;
}

?>