
<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);

    exit;
}

$input = json_decode(file_get_contents("php://input"), true);

if (
    !is_array($input) ||
    empty($input["name"]) ||
    empty($input["email"]) ||
    empty($input["password"]) ||
    empty($input["birth_date"]) ||
    empty($input["trainer_id"])
) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "All fields are required."
    ]);

    exit;
}

$name = trim($input["name"]);
$email = trim($input["email"]);
$password = $input["password"];
$birth_date = $input["birth_date"];
$trainer_id = $input["trainer_id"];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid email address."
    ]);

    exit;
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo->beginTransaction();

    // Insert Account
    $sql = "INSERT INTO account (email, password_hash, role)
            VALUES (:email, :password_hash, 'UMAMUSUME')";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":email" => $email,
        ":password_hash" => $password_hash
    ]);

    $account_id = $pdo->lastInsertId();

    // Insert Umamusume Profile
    $sql = "INSERT INTO umamusume
            (account_id, trainer_id, name, birth_date)
            VALUES (:account_id, :trainer_id, :name, :birth_date)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":account_id" => $account_id,
        ":trainer_id" => $trainer_id,
        ":name" => $name,
        ":birth_date" => $birth_date
    ]);

    $umamusume_id = $pdo->lastInsertId();

    $pdo->commit();

    http_response_code(201);

    echo json_encode([
        "success" => true,
        "message" => "Umamusume created successfully!",
        "account_id" => $account_id,
        "umamusume_id" => $umamusume_id
    ]);

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to create Umamusume."
    ]);
}
