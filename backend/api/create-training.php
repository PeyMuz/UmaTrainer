<?php

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);

    exit;
}

require_once __DIR__ . "/../config/database.php";

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (
    !is_array($input) ||
    empty($input["umamusume_id"]) ||
    empty($input["training_type"]) ||
    empty($input["training_name"])
) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Umamusume ID, training type, and training name are required."
    ]);

    exit;
}

$umamusume_id = $input["umamusume_id"];
$training_type = trim($input["training_type"]);
$training_name = trim($input["training_name"]);

//to validate Umamusume ID

if (!filter_var($umamusume_id, FILTER_VALIDATE_INT) || $umamusume_id <=0) {
    http_response_code(400);

    echo json_encode([
       "success" => false,
       "message" => "Umamusume ID error. Please try again."
    ]); 

    exit;
}

// Normalize training type
$training_type = strtoupper($training_type);

// Validate Training Type
$allowed_types = ["SPEED", "STAMINA", "POWER", "GUTS", "WITS"];


if (!in_array($training_type, $allowed_types, true)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid training type."
    ]);

    exit;
}

//Check if the umamusume exist

$sql = "SELECT umamusume_id
        FROM umamusume
        WHERE umamusume_id = :umamusume_id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":umamusume_id" => $umamusume_id
]);

$existing_umamusume = $stmt->fetchColumn();

if (!$existing_umamusume) {
    http_response_code(404);

    echo json_encode([
        "success" => false,
        "message" => "Umamusume not found."
    ]);

    exit;
}

try{
    $sql = "INSERT INTO training (
                umamusume_id,
                training_type,
                training_name
            )
            VALUES (
                :umamusume_id,
                :training_type,
                :training_name
            )";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":umamusume_id" => $umamusume_id,
        ":training_type" => $training_type,
        ":training_name" => $training_name
    ]);

    $training_id = $pdo->lastInsertId();
    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Training created successfully!",
        "training_id" => $training_id
    ]);
} catch (PDOException $e) {

    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to create Training. Please try again"
    ]);
}

?>