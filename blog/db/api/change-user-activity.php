<?php
// Informacja dla klienta (np. przegladarki), ze odpowiedz bedzie w formacie JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Origin: *");

require_once "../db-connect.php";

if ($_SERVER["REQUEST_METHOD"] == "PATCH") {
    // Odbieramy dane z ciala zadania
    $inputData = json_decode(file_get_contents("php://input"));

    // Sprawdzamy czy przekazano id i aktuwalna wartosc aktywnosci konta
    if (!isset($inputData->userId) || !isset($inputData->activity)) {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Invalid type parameter"]);
        exit();
    }

    $userId = $inputData->userId;
    $currentActivity = $inputData->activity;
    $conn = null;
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $conn->begin_transaction();

        $query = "SELECT is_active FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 0) {
            http_response_code(404); // Not Found
            echo json_encode(["success" => false, "message" => "Nie ma użytkownika o id $userId"]);
            $conn->rollback();
            exit();
        }

        $row = $result->fetch_object();
        $dbActivity = $row->is_active;
        $newActivity = $currentActivity == 1 ? 0 : 1;

        // Sprawdzamy, czy aktualizacja jest potrzebna
        if ($dbActivity == $newActivity) {
            http_response_code(200); // OK
            echo json_encode([ "success" => false, "message" => "Nie wprowadzono zmian. Stan is_active użytkownika $userId już wynosi $newActivity"]);
            $conn->rollback();
            exit();
        }

        $query = "UPDATE users SET is_active = ? WHERE user_id = ?;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $newActivity, $userId);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            http_response_code(200); // OK
            $newActivityTxt = $newActivity == 1 ? "Aktywne" : "Nieaktywne";
            echo json_encode(["success" => true, "message" => "Zmieniono aktywność konta użytkownika $userId na \"$newActivityTxt\""]);
        }
        else {
            http_response_code(404); // Not Found
            echo json_encode(["success" => false, "message" => "Nie ma użytkownika o id $userId"]);
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
} // if REQUEST_METHOD PATCH
else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}