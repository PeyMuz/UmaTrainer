<?php

header("Content-Type: application/json");

//to Validate HTTP
if ($_SERVER["REQUEST_METHOD"] !== "DELETE"){
   http_response_code(405);

   echo json_encode([
     "success" => false,
     "message" => "Method not allowed. Please try again, torena san~"
   ]);

   exit;
}

require_once __DIR__ . "/../config/database.php";

//to get trainer ID
$trainer_id = $_GET["id"] ?? null;

if (!$trainer_id || !filter_var($trainer_id, FILTER_VALIDATE_INT)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid Trainer ID."
    ]);

    exit;
}


$sql = "SELECT COUNT(*)
       FROM umamusume
       WHERE trainer_id = :trainer_id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
   ":trainer_id" => $trainer_id
 ]);

$umamusume_count = $stmt->fetchColumn();

if ($umamusume_count > 0) {
    http_response_code(409);

    echo json_encode([
     "success" => false,
     "message" => "Cannot delete Trainer. There are assigned Umamusume records."
    ]);

    exit;
}

// Get the account_id connected to this Trainer
$sql = "SELECT account_id
        FROM trainer
        WHERE trainer_id = :trainer_id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
  ":trainer_id" => $trainer_id
]);

$account_id = $stmt->fetchColumn();


//check if the trainer exists

if (!$account_id){
   http_response_code(404);

   echo json_encode([
    "success" => false,
    "message" => "Error. Toren-san not found."
   ]);

   exit();
}


try{

   $pdo->beginTransaction();

   //Delete trainer profile
   $sql="DELETE FROM trainer
         WHERE trainer_id = :trainer_id";
   
   $stmt = $pdo->prepare($sql);

   $stmt->execute([
     ":trainer_id" =>$trainer_id
   ]);

   // Delete associated Account

   $sql = "DELETE FROM account
          WHERE account_id = :account_id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":account_id" => $account_id
    ]);

    $pdo->commit();

    http_response_code(200);

     echo json_encode([
        "success" => true,
        "message" => "Trainer deleted successfully!"
    ]);
}catch (PDOException $e){
   if($pdo->inTransaction()){
      $pdo->rollBack();
   }

    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to delete Trainer. Please try again."
    ]);
}
?>