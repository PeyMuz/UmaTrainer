<?php

header("Content-Type: application/json");

require_once __DIR__ . "/config/database.php";

try {
    $pdo->beginTransaction();

    $email = "trainer@example.com";
    $password = password_hash("Trainer123!", PASSWORD_DEFAULT);

    $sql = "INSERT INTO account (email, password_hash, role)
            VALUES (:email, :password_hash, 'TRAINER')";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":email" => $email,
        ":password_hash" => $password
    ]);

    $account_id = $pdo->lastInsertId();

    $sql = "INSERT INTO trainer (account_id, trainee_name)
            VALUES (:account_id, :trainee_name)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":account_id" => $account_id,
        ":trainee_name" => "Test Trainer"
    ]);

    $trainer_id = $pdo->lastInsertId();

    $pdo->commit();

    echo json_encode([
        "success" => true,
        "message" => "Test Trainer created successfully!",
        "account_id" => $account_id,
        "trainer_id" => $trainer_id
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to create test Trainer."
    ]);
}