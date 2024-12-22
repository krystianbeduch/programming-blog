<?php
// Informacja dla klienta (np. przegladarki), ze odpowiedz bedzie w formacie JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Origin: *");

require_once "../db-connect.php";

if ($_SERVER["REQUEST_METHOD"] == "PATCH") {
    // Odbieramy dane z ciala zadania
    $inputData = json_decode(file_get_contents("php://input"));

//    echo json_encode($inputData);
    // Sprawdzamy czy przekazano id, username, email i role
    if (!isset($inputData->id) || !isset($inputData->username) || !isset($inputData->email) || !isset($inputData->role)) {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Invalid type parameter"]);
        exit();
    }

//    $id = $inputData->id;
//    $username = $inputData->username;
//    $email = $inputData->email;
//    $role = $inputData->role;

    $query = "UPDATE users SET username = ?, email = ?, role_id = ?";

    // Sprawdzamy czy przekazano about-me
    if (isset($inputData->aboutMe)) {
//        $aboutMe = $inputData->aboutMe;
        $query .= ", about_me = ?";
    }

    // Sprawdzamy czy przekazano haslo
    if (isset($inputData->password)) {
        $hashedPassword = password_hash($inputData->password, PASSWORD_DEFAULT);
        $query .= ", password = ?";
    }
    $query .= " WHERE user_id = ?";
    $conn = null;
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
//        if ($type == "post") {
//            // Usuwamy post
//            $query = "DELETE FROM posts WHERE post_id = ?";
//        }
//        else if ($type == "user") {
//            $query = "DELETE FROM users WHERE user_id = ?";
//            $type = "użytkownik";
//        }
//        else {
//            // Nieznana operacja
//            http_response_code(400);
//            echo json_encode(["success" => false, "message" => "Invalid type parameter"]);
//            exit();
//        }
//        $query =
        $conn->begin_transaction();
        $stmt = $conn->prepare($query);
        if (isset($inputData->password)) {
            if (isset($inputData->aboutMe)) {
                $stmt->bind_param(
                    "ssissi",
                    $inputData->username,
                    $inputData->email,
                    $inputData->role,
                    $inputData->aboutMe,
                    $hashedPassword,
                    $inputData->id
                );
            }
            else {
                $stmt->bind_param(
                    "ssisi",
                    $inputData->username,
                    $inputData->email,
                    $inputData->role,
                    $hashedPassword,
                    $inputData->id
                );
            }
        }// if isset($inputData->password)
        else {
            if (isset($inputData->aboutMe)) {
                $stmt->bind_param(
                    "ssisi",
                    $inputData->username,
                    $inputData->email,
                    $inputData->role,
                    $inputData->aboutMe,
                    $inputData->id
                );
            }
            else {
                $stmt->bind_param(
                    "ssii",
                    $inputData->username,
                    $inputData->email,
                    $inputData->role,
                    $inputData->id
                );
            }
        } // else

//    }
//        $stmt->bind_param("i", $id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(["success" => true, "message" => "Zaktualizowano użytkownika o id " . $inputData->id]);
        }
        else {
            http_response_code(404); // Not Found
            echo json_encode(["success" => false, "message" => "Brak aktualizacji"]);
        }
        $conn->commit();
    } // try
    catch (Exception $e) {
        $conn->rollback();
        http_response_code(500); // Internal Server Error - blad polaczenia z serwerem
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
        exit();
    }
    finally {
        $stmt->close();
        $conn->close();
    }
} // if REQUEST_METHOD DELETE
else {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}