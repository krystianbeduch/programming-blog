<?php
// Informacja dla klienta (np. przegladarki), ze odpowiedz bedzie w formacie JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Origin: *");

require_once "../db-connect.php";

//use mysqli;
//use Exception;

// Funkcja do zmiany aktywności użytkownika
function changeUserActivity(int $userId, int $currentActivity): void {
    $conn = new mysqli(
        MySQLConfig::SERVER,
        MySQLConfig::USER,
        MySQLConfig::PASSWORD,
        MySQLConfig::DATABASE
    );

    try {
        $conn->begin_transaction();

        // Pobieramy aktualny stan aktywności użytkownika
        $query = "SELECT is_active FROM users WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            http_response_code(404); // Not Found
            echo json_encode(["success" => false, "message" => "Nie ma użytkownika o id $userId"]);
            return;
        }

        $row = $result->fetch_object();
        $dbActivity = $row->is_active;
        $newActivity = $currentActivity === 1 ? 0 : 1;

        // Sprawdzamy, czy aktualizacja jest potrzebna
        if ($dbActivity === $newActivity) {
            http_response_code(400); // Bad Request
            echo json_encode(["success" => false, "message" => "Stan aktywności użytkownika $userId już wynosi $newActivity"]);
            return;
        }

        // Aktualizujemy stan aktywności
        $query = "UPDATE users SET is_active = ? WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $newActivity, $userId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            http_response_code(200); // OK
            $newActivityTxt = $newActivity === 1 ? "Aktywne" : "Nieaktywne";
            echo json_encode(["success" => true, "message" => "Zmieniono aktywność konta użytkownika $userId na \"$newActivityTxt\""]);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["success" => false, "message" => "Nie ma użytkownika o id $userId"]);
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500); // Internal Server Error
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    } finally {
        $stmt->close();
        $conn->close();
    }
}

// Funkcja do edytowania danych użytkownika
function editUser(array $inputData): void {
    $query = "UPDATE users SET username = ?, email = ?, role_id = ?";

    if (isset($inputData['aboutMe'])) {
        $query .= ", about_me = ?";
    }

    if (isset($inputData['password'])) {
        $hashedPassword = password_hash($inputData['password'], PASSWORD_DEFAULT);
        $query .= ", password = ?";
    }
    $query .= " WHERE user_id = ?";

    $conn = new mysqli(
        MySQLConfig::SERVER,
        MySQLConfig::USER,
        MySQLConfig::PASSWORD,
        MySQLConfig::DATABASE
    );

    try {
        $conn->begin_transaction();

        $stmt = $conn->prepare($query);
        if (isset($inputData['password'])) {
            if (isset($inputData['aboutMe'])) {
                $stmt->bind_param(
                    "ssissi",
                    $inputData['username'],
                    $inputData['email'],
                    $inputData['role'],
                    $inputData['aboutMe'],
                    $hashedPassword,
                    $inputData['id']
                );
            } else {
                $stmt->bind_param(
                    "ssisi",
                    $inputData['username'],
                    $inputData['email'],
                    $inputData['role'],
                    $hashedPassword,
                    $inputData['id']
                );
            }
        } else {
            if (isset($inputData['aboutMe'])) {
                $stmt->bind_param(
                    "ssisi",
                    $inputData['username'],
                    $inputData['email'],
                    $inputData['role'],
                    $inputData['aboutMe'],
                    $inputData['id']
                );
            } else {
                $stmt->bind_param(
                    "ssii",
                    $inputData['username'],
                    $inputData['email'],
                    $inputData['role'],
                    $inputData['id']
                );
            }
        }

        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            http_response_code(200); // OK
            echo json_encode(["success" => true, "message" => "Zaktualizowano użytkownika o id " . $inputData['id']]);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["success" => false, "message" => "Brak aktualizacji"]);
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500); // Internal Server Error
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    } finally {
        $stmt->close();
        $conn->close();
    }
}

// Funkcja do usuwania użytkownika, posta lub komentarza
function deleteContent(string $type, int $id): void {
    $conn = new mysqli(
        MySQLConfig::SERVER,
        MySQLConfig::USER,
        MySQLConfig::PASSWORD,
        MySQLConfig::DATABASE
    );

    try {
        $query = match ($type) {
            'post' => "DELETE FROM posts WHERE post_id = ?",
            'user' => "DELETE FROM users WHERE user_id = ?",
            'comment' => "DELETE FROM comments WHERE comment_id = ?",
            default => throw new Exception("Invalid type parameter"),
        };

        $conn->begin_transaction();

        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            http_response_code(200); // OK
            echo json_encode(["success" => true, "message" => "Usunięto {$type} o id $id"]);
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["success" => false, "message" => "Nie ma {$type} o id $id"]);
        }

        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        http_response_code(500); // Internal Server Error
        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
    } finally {
        $stmt->close();
        $conn->close();
    }
}

// Obsługa zapytań
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $inputData = json_decode(file_get_contents('php://input'), true);
    if (isset($inputData['userId']) && isset($inputData['activity'])) {
        changeUserActivity((int) $inputData['userId'], (int) $inputData['activity']);
    } elseif (isset($inputData['id']) && isset($inputData['username']) && isset($inputData['email']) && isset($inputData['role'])) {
        editUser($inputData);
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Invalid input data"]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $inputData = json_decode(file_get_contents('php://input'), true);
    if (isset($inputData['type']) && isset($inputData['id'])) {
        deleteContent($inputData['type'], (int) $inputData['id']);
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(["success" => false, "message" => "Invalid input data"]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}