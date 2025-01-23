<?php
// Informacja dla klienta (np. przegladarki), ze odpowiedz bedzie w formacie JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Origin: *");

require_once "../mysql-operation.php";
require_once "../../errors/error-codes.php";

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    http_response_code(HttpStatus::METHOD_NOT_ALLOWED);
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}

// Pobierz parametry z URL
$type = $_GET["type"] ?? null;
$value = $_GET["value"] ?? null;

// Walidacja parametrow
if (!$type || !$value) {
    http_response_code(HttpStatus::BAD_REQUEST);
    echo json_encode(["success" => false, "message" => "Invalid request parameters"]);
    exit();
}

if (!in_array($type, ["username", "email"], )) {
    http_response_code(HttpStatus::BAD_REQUEST);
    echo json_encode(["success" => false, "message" => "Invalid type parameter"]);
    exit();
}

$conn = null;
$stmt = null;
try {
    $conn = createMySQLiConnection();
    $query = [
        "username" => "SELECT COUNT(*) FROM users WHERE username = ?;",
        "email" => "SELECT COUNT(*) FROM users WHERE email = ?;",
    ];

    // Przygotowanie zapytania SQL
    $stmt = $conn->prepare($query[$type]);
    $stmt->bind_param("s", $value);
    $stmt->bind_result($count);
    $stmt->execute();
    $stmt->fetch();

    // Zwrot odpowiedzi w formacie JSON
    $response = [
        "success" => $count == 0,
        "message" => ucfirst($type) . ($count > 0 ? " is already taken" : " is available")
    ];
    echo json_encode($response);
}
catch (Exception $e) {
    http_response_code(HttpStatus::INTERNAL_SERVER_ERROR);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
finally {
    $stmt?->close();
    $conn?->close();
}