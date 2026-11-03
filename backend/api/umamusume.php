<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";

try{
    $sql = "SELECT * FROM umamusume";

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