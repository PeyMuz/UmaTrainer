<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../config/database.php";

try{
    $sql="SELECT * FROM training";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $trainings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "success" => true,
        "count" => count($trainings),
        "data" => $trainings
    ]);
    
}catch (PDOException $e){
   
  error_log($e->getMessage());

  http_response_code(500);


    echo json_encode([
        "success" => false,
        "message" => "Failed to retrieve Training records."
    ]);
}


?>