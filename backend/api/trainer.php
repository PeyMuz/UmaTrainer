<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";

try{

  $sql = "SELECT
            trainer.trainer_id,
            trainer.trainee_name,
            account.email,
            account.role
        FROM trainer
        INNER JOIN account
        ON trainer.account_id = account.account_id";

  $stmt = $pdo->prepare($sql);
  $stmt->execute();

  $trainers = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode([
    "success" => true,
    "count" => count($trainers),
    "data" => $trainers
  ]);
}catch(PDOException $e){
    http_response_code(500);

    echo json_encode([
      "success" => false,
      "message" => "Failed to get the trainer data. Try again"
    ]);
}

?>