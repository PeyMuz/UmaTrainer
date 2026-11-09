
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

// Get Trainer ID
$trainer_id = $_GET["id"] ?? null;

// Validate Trainer ID
if (!$trainer_id || !filter_var($trainer_id, FILTER_VALIDATE_INT)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid Trainer ID. Please try again."
    ]);

    exit;
}

// Get JSON input
$input = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (
    !is_array($input) ||
    empty($input["trainee_name"]) ||
    empty($input["email"])
) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Trainer name and email are required."
    ]);

    exit;
}

// Extract values
$trainee_name = trim($input["trainee_name"]);
$email = trim($input["email"]);

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid email address."
    ]);

    exit;
}

try {

    // Get the account_id connected to this Trainer
    $sql = "SELECT account_id
            FROM trainer
            WHERE trainer_id = :trainer_id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":trainer_id" => $trainer_id
    ]);

    $account_id = $stmt->fetchColumn();

    // Check if Trainer exists
    if (!$account_id) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Trainer not found. Please try again."
        ]);

        exit;
    }

    $pdo->beginTransaction();

    // Update Trainer Name
    $sql = "UPDATE trainer
            SET trainee_name = :trainee_name
            WHERE trainer_id = :trainer_id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":trainee_name" => $trainee_name,
        ":trainer_id" => $trainer_id
    ]);

    // Update Account Email
    $sql = "UPDATE account
            SET email = :email
            WHERE account_id = :account_id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":email" => $email,
        ":account_id" => $account_id
    ]);

    $pdo->commit();

    http_response_code(200);

    echo json_encode([
        "success" => true,
        "message" => "Trainer updated successfully!"
    ]);

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to update Trainer. Please try again."
    ]);
}
