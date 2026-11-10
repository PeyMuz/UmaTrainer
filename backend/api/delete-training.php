<?php

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);

    exit;
}

require_once __DIR__ . "/../config/database.php";

//cheeck if the training ID is valid

$training_id = $_GET["id"] ?? null;

if (!$training_id || !filter_var($training_id, FILTER_VALIDATE_INT) || $training_id <= 0) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid Training ID."
    ]);

    exit;
}

$sql = "SELECT training_id
        FROM training
        WHERE training_id = :training_id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
  ":training_id" => $training_id
]);

$existing_training = $stmt->fetchColumn();

if(!$existing_training) {
    http_response_code(404);

    echo json_encode([
        "success" => false,
        "message" => "Inavlid Training type. Please try again."
    ]);

    exit;
}

try {

   $sql = "DELETE FROM training
          WHERE training_id = :training_id";

    $stmt = $pdo->prepare($sql);

     $stmt->execute([
        ":training_id" => $training_id
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Training deleted successfully!"
    ]);

} catch(PDOException $e){
   
   error_log($e->getMessage());

   http_response_code(500);

   echo json_encode([
        "success" => false,
        "message" => "Failed to delete Training."
    ]);
}



?>

