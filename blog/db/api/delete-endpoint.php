<?php
// Informacja dla klienta (np. przegladarki), ze odpowiedz bedzie w formacie JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Origin: *");

require_once "../db-connect.php";

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    // Odbieramy dane z ciala zadania
    $inputData = json_decode(file_get_contents("php://input"));

    // Sprawdzamy czy przekazano typ operacji i ID
    if (!isset($inputData->type) || !isset($inputData->id)) {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Invalid type parameter"]);
        exit();
    }

    $type = $inputData->type;
    $id = $inputData->id;
    $conn = null;
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        if ($type == "post") {
            // Usuwamy post
            $query = "DELETE FROM posts WHERE post_id = ?";
        }
        else if ($type == "user") {
            $query = "DELETE FROM users WHERE user_id = ?";
            $type = "użytkownik";
        }
        else {
            // Nieznana operacja
            http_response_code(400); // Bad Request
            echo json_encode(["success" => false, "message" => "Invalid type parameter"]);
            exit();
        }
        $conn->begin_transaction();
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            http_response_code(200); // OK
            echo json_encode(["success" => true, "message" => "Usunięto {$type}a o id " . $id]);
        }
        else {
            http_response_code(404); // Not Found
            echo json_encode(["success" => false, "message" => "Nie ma {$type}a o id " . $id]);
        }
        $conn->commit();
    } // try
    catch (Exception $e) {
        $conn->rollback();
        http_response_code(500); // Internal Server Error - blad polaczenia z serwerem
        echo json_encode(["success" => false, "message" => "Error: " .$e->getMessage()]);
        exit();
    }
    finally {
        $stmt->close();
        $conn->close();
    }
} // if REQUEST_METHOD DELETE
else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}