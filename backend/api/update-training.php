<?php

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);

    exit;
}

require_once __DIR__ . "/../config/database.php";

//for id vaidation

$training_id = $_GET["id"] ?? null;

if (!$training_id || !filter_var($training_id, FILTER_VALIDATE_INT) || $training_id <= 0) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid Training ID."
    ]);

    exit;
}

//checking for training type and training name

$input = json_decode(file_get_contents("php://input"), true);

if(
    !is_array($input) ||
    empty($input["training_type"]) ||
    empty($input["training_name"])
){
    http_response_code(401);

    echo json_encode([
      "success" => false,
      "message" => "training type and training name required."
    ]);

    exit;
}

$training_type = strtoupper(trim($input["training_type"]));
$training_name = trim($input["training_name"]);

$allowed_types = ["SPEED", "STAMINA", "POWER", "GUTS", "WITS"];

if (!in_array($training_type, $allowed_types, true)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid training type."
    ]);

    exit;
}

$sql = "SELECT training_id
        FROM training
        WHERE training_id = :training_id";

$stmt = $pdo->prepare($sql);

$stmt -> execute([
    ":training_id" => $training_id
]);

$existing_training = $stmt->fetchColumn();

if (!$existing_training) {
    http_response_code(404);

    echo json_encode([
        "success" => false,
        "message" => "Training not found."
    ]);

    exit;
}


try{
$sql = "UPDATE training
            SET
                training_type = :training_type,
                training_name = :training_name
            WHERE training_id = :training_id";

$stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":training_type" => $training_type,
        ":training_name" => $training_name,
        ":training_id" => $training_id
    ]);

    echo json_encode([
      "success" => true,
      "message" => "Training updated successfully!"
    ]);

} catch (PDOException $e) {

    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to update Training."
    ]);
}

?>