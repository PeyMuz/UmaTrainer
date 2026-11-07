
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

$umamusume_id = $_GET["id"] ?? null;

if (!$umamusume_id || !filter_var($umamusume_id, FILTER_VALIDATE_INT)) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid Umamusume ID."
    ]);

    exit;
}

try {
    $sql = "DELETE FROM umamusume
            WHERE umamusume_id = :umamusume_id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":umamusume_id" => $umamusume_id
    ]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);

        echo json_encode([
            "success" => false,
            "message" => "Umamusume not found."
        ]);

        exit;
    }

    http_response_code(200);

    echo json_encode([
        "success" => true,
        "message" => "Umamusume deleted successfully!"
    ]);

} catch (PDOException $e) {
    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to delete Umamusume."
    ]);

    exit;
}
