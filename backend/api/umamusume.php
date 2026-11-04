<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";

try{
    $sql = "SELECT
            umamusume.umamusume_id,
            umamusume.name,
            umamusume.birth_date,
            account.email,
            trainer.trainer_id,
            trainer.trainee_name
        FROM umamusume
        INNER JOIN account
            ON umamusume.account_id = account.account_id
        INNER JOIN trainer
            ON umamusume.trainer_id = trainer.trainer_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $umamusumes = $stmt->fetchAll(PDO::FETCH_ASSOC); //to test data in associative array

    echo json_encode([
       "success" => true,
       "count" => count($umamusumes),
       "data" => $umamusumes    
    ]);

    } catch (PDOException $e) {
        http_response_code(500);

        echo json_encode([
            "success" => false,
            "message" => "Failed to retrieve Umamusume records."
        ]);
    }

?>