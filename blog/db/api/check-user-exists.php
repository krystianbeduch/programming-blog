<?php
// Informacja dla klienta (np. przegladarki), ze odpowiedz bedzie w formacie JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Origin: *");

require_once "../db-connect.php";
require_once "../../errors/error-codes.php";

// Pobierz parametry z URL
$username = $_GET["username"] ?? null;

// Walidacja parametru
if (!$username) {
    http_response_code(HttpStatus::BAD_REQUEST);
    echo json_encode(["success" => false, "message" => "Invalid request parameters"]);
    exit();
}

$conn = null;
$stmt = null;
try {
    $conn = new mysqli(
        MySQLConfig::SERVER,
        MySQLConfig::USER,
        MySQLConfig::PASSWORD,
        MySQLConfig::DATABASE
    );

    // Przygotowanie zapytania SQL
    $query = <<<SQL
    SELECT 
        COUNT(*) 
    FROM users 
    WHERE username = ?;
    SQL;
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->bind_result($count);
    $stmt->execute();
    $stmt->fetch();

    // Zwrot odpowiedzi w formacie JSON
    $response = [
        "success" => $count == 1,
        "message" => $count == 1 ? "User exists" : "User does not exists"
    ];
    echo json_encode($response);
}
catch (Exception $e) {
    http_response_code(HttpStatus::INTERNAL_SERVER_ERROR);
    echo json_encode(["success" => false, "message" => "Error: " .$e->getMessage()]);
    exit();
}
finally {
    $stmt?->close();
    $conn?->close();
}