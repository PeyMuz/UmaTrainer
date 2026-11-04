<?php

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
       "success" => false,
       "message" => "Method not allowed"
    ]);

    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (
    empty($input["name"]) ||
    empty($input["email"]) ||
    empty($input["password"]) ||
    empty($input["birthdate"]) ||
    empty($input["trainer_id"]) 
){
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "All fields are required."
    ]);

    exit;
}


?>