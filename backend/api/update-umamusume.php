
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

$umamusume_id = $_GET["id"] ?? null;

// Validate Umamusume ID
if (!$umamusume_id || !filter_var($umamusume_id, FILTER_VALIDATE_INT) || $umamusume_id <= 0) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Invalid Umamusume ID."
    ]);

    exit;
}

// Receive JSON body
$input = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (
    !is_array($input) ||
    empty($input["name"]) ||
    empty($input["birth_date"])
) {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Name and birth date are required."
    ]);

    exit;
}

// Extract values
$name = trim($input["name"]);
$birth_date = $input["birth_date"];

if ($name === "") {
    http_response_code(400);

    echo json_encode([
        "success" => false,
        "message" => "Name cannot be empty."
    ]);

    exit;
}

try {
    $sql = "UPDATE umamusume
            SET name = :name,
                birth_date = :birth_date
            WHERE umamusume_id = :umamusume_id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":name" => $name,
        ":birth_date" => $birth_date,
        ":umamusume_id" => $umamusume_id
    ]);

    if ($stmt->rowCount() === 0) {
        $check = $pdo->prepare(
            "SELECT umamusume_id FROM umamusume WHERE umamusume_id = :id"
        );

        $check->execute([":id" => $umamusume_id]);

        if (!$check->fetch()) {
            http_response_code(404);

            echo json_encode([
                "success" => false,
                "message" => "Umamusume not found."
            ]);

            exit;
        }
    }

    http_response_code(200);

    echo json_encode([
        "success" => true,
        "message" => "Umamusume updated successfully!"
    ]);

} catch (PDOException $e) {
    error_log($e->getMessage());

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Failed to update Umamusume."
    ]);
}

?>
