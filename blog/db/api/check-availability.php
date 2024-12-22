<?php
// Informacja dla klienta (np. przegladarki), ze odpowiedz bedzie w formacie JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: GET");
//$allowed_origins = ['http://localhost', 'http://127.0.0.1'];

//if (in_array($_SERVER['HTTP_ORIGIN'], $allowed_origins)) {
//    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
//}

header("Access-Control-Allow-Origin: *");

require_once "../db-connect.php";

// Pobierz parametry z URL
$type = $_GET["type"] ?? null;
$value = $_GET["value"] ?? null;

// Walidacja parametrow
if (!$type || !$value) {
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

    $query = "";
    if ($type == "username") {
        $query = "SELECT COUNT(*) FROM users WHERE LOWER(username) = LOWER(?)";
    }
    else if ($type == "email") {
        $query = "SELECT COUNT(*) FROM users WHERE LOWER(email) = LOWER(?)";
    }
    else {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Invalid type parameter"]);
        exit();
    }

    // Przygotowanie zapytania SQL
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $value);
    $stmt->bind_result($count);
    $stmt->execute();
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    // Zwrot odpowiedzi w formacie JSON
    if ($count > 0) {
        echo json_encode(["success" => false, "message" => ucfirst($type) . " is already taken"]);
    }
    else {
        echo json_encode(["success" => true, "message" => ucfirst($type) . " is available"]);
    }
}
catch (Exception $e) {
    http_response_code(500); // Internal Server Error - blad polaczenia z serwerem
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    exit;
}