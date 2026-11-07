<?php

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] !== "POST"){
    http_response_code(405);

    echo json_encode([
        "success" => false,
        "message" => "Method not allowed."
    ]);

    exit;
}

require_once __DIR__ . "/../config/database.php";

$input = json_decode(file_get_contents("php://input"), true);

if (

  !is_array($input) ||
  empty($input["trainee_name"]) ||
  empty($input["email"]) ||
  empty($input["password"])

){
    http_response_code(400);

    echo json_encode([
       "success" => false,
       "message" => "All fields are required."
    ]); 

    exit;

}

$trainee_name = trim($input["trainee_name"]);
$email = trim($input["email"]);
$password = $input["password"];


//to secure email

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

    //Insert Account
    $sql= "INSERT INTO account (email, password_hash, role)
           VALUES (:email, :password, 'TRAINER')";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":email" => $email,
        ":password" => $password_hash
    ]);

    $account_id = $pdo->lastInsertId();

    //to insert TRAINER profile
    $sql= "INSERT INTO trainer (account_id, trainee_name)
           VALUES (:account_id, :trainee_name)";

    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
       ":account_id" => $account_id,
       ":trainee_name" => $trainee_name
    ]);

    $trainer_id = $pdo->lastInsertId();

    $pdo->commit();

} catch (PDOException $e) {

    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to create Trainer."
    ]);

    exit;
}

//if succesful
http_response_code(201);

echo json_encode([
    "success" => true,
    "message" => "Trainer created successfully!",
    "account_id" => $account_id,
    "trainer_id" => $trainer_id
]);


?>