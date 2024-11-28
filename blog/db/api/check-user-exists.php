<?php
// Informacja dla klienta (np. przegladarki), ze odpowiedz bedzie w formacie JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Origin: *");



require_once "../db-connect.php";

// Pobierz parametry z URL
$username = $_GET["username"] ?? null;

// Walidacja parametru
if (!$username) {
    http_response_code(400); // Bad request - bledna skladnia
    echo json_encode(["success" => false, "message" => "Invalid request parameters"]);
    exit();
}

try {
    $conn = new mysqli(
        MySQLConfig::SERVER,
        MySQLConfig::USER,
        MySQLConfig::PASSWORD,
        MySQLConfig::DATABASE
    );

    // Przygotowanie zapytania SQL
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE LOWER(username) = LOWER(?)");
    $stmt->bind_param("s", $username);
    $stmt->bind_result($count);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    // Zwrot odpowiedzi w formacie JSON
    if ($count == 1) {
        echo json_encode(["success" => true, "message" => "User exists"]);
    }
    else {
        echo json_encode(["success" => false, "message" => "User is not exists"]);
    }
}
catch (Exception $e) {
    http_response_code(500); // Internal Server Error - blad polaczenia z serwerem
    echo json_encode(["success" => false, "message" => "Error: $e"]);
    exit;
}
?>
