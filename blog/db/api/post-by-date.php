<?php
// Informacja dla klienta (np. przegladarki), ze odpowiedz bedzie w formacie JSON
header("Content-Type: application/json");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Origin: *");

require_once "../mysql-operation.php";
require_once "../../errors/error-codes.php";

// Obsluga zapytan
if ($_SERVER["REQUEST_METHOD"] != "GET") {
    http_response_code(HttpStatus::METHOD_NOT_ALLOWED);
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
    exit();
}

$category = $_GET["category"] ?? null;
$startDate = $_GET["startDate"] ?? null;
$endDate = $_GET["endDate"] ?? null;

if (!$category || !$startDate) {
    http_response_code(HttpStatus::BAD_REQUEST);
    echo json_encode(["success" => false, "message" => "Invalid request parameters"]);
    exit();
}

$conn = null;
$stmt = null;
try {
    $conn = createMySQLiConnection();
    $query = <<<SQL
    SELECT 
        p1.post_id,
        c2.category_name,
        p1.title, 
        p1.content,
        p1.created_at, 
        p1.updated_at, 
        u.username, 
        u.email,
        COUNT(c1.post_id) AS 'comments_count',
        p2.file_data, 
        p2.file_type 
    FROM posts p1 
    JOIN users u ON p1.user_id = u.user_id 
    LEFT JOIN posts_attachments p2 ON p1.attachment_id = p2.attachment_id
    LEFT JOIN comments c1 ON p1.post_id = c1.post_id
    JOIN categories c2 ON p1.category_id = c2.category_id
    WHERE c2.category_name = ?
    SQL;

    if (!$endDate) {
        $query .= " AND DATE(p1.created_at) = ? ";
//        echo json_encode("tal");
    }
    else {
        $query .= " AND DATE(p1.created_at) BETWEEN ? AND ? ";
    }
    $query .= " GROUP BY 
                    p1.post_id, 
                    c2.category_name,
                    p1.title, 
                    p1.content,
                    p1.created_at, 
                    p1.updated_at, 
                    u.username, 
                    u.email,
                    p2.file_data, 
                    p2.file_type
                ORDER BY p1.updated_at DESC;";

    $stmt = $conn->prepare($query);
    $startDateSQL = date("Y-m-d", strtotime($startDate));
    if (!$endDate) {
        $stmt->bind_param("ss", $category, $startDateSQL);
    }
    else {
        $endDateSQL = date("Y-m-d", strtotime($endDate));
        $stmt->bind_param("sss", $category, $startDateSQL, $endDateSQL);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        if (isset($row["file_data"])) {
            $row["file_data"] = base64_encode($row["file_data"]); // Zakoduj dane BLOB
        }
        $data[] = $row;
    }
    if ($data) {
        http_response_code(HttpStatus::OK);
        echo json_encode(["success" => true, "posts" => $data]);
    }
    else {
        http_response_code(HttpStatus::NOT_FOUND);
        echo json_encode(["success" => false, "message" => "Posts not found"]);;
    }
}
catch (Exception $e) {
    http_response_code(HttpStatus::INTERNAL_SERVER_ERROR);
    echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
}
finally {
    $stmt?->close();
    $conn?->close();
}

//$inputData = json_decode(file_get_contents("php://input"));

//    if (isset($inputData->userId) && isset($inputData->activity)) {
//        changeUserActivity((int) $inputData->userId, (int) $inputData->activity);
//    }
//    elseif (isset($inputData->id) && isset($inputData->username) && isset($inputData->email) && isset($inputData->role)) {
//        editUser($inputData);
//    }
//    else {
//        http_response_code(HttpStatus::BAD_REQUEST);
//        echo json_encode(["success" => false, "message" => "Invalid input data"]);
//    }

// Funkcja do zmiany aktywnosci konta uzytkownika
//function changeUserActivity(int $userId, int $currentActivity): void {
//    $conn = null;
//    $stmt = null;
//    try {
//        $conn = createMySQLiConnection();
//
//        $conn->begin_transaction();
//
//        // Pobieramy aktualny stan aktywności użytkownika
//        $query = <<<SQL
//        SELECT
//            is_active
//        FROM users
//        WHERE user_id = ?;
//        SQL;
//        $stmt = $conn->prepare($query);
//        $stmt->bind_param("i", $userId);
//        $stmt->execute();
//        $result = $stmt->get_result();
//
//        if ($result->num_rows == 0) {
//            http_response_code(HttpStatus::NOT_FOUND);
//            echo json_encode(["success" => false, "message" => "Nie ma użytkownika o id $userId"]);
//            return;
//        }
//
//        $row = $result->fetch_object();
//        $dbActivity = $row->is_active;
//        $newActivity = $currentActivity == 1 ? 0 : 1;
//
//        // Sprawdzamy, czy aktualizacja jest potrzebna
//        if ($dbActivity == $newActivity) {
//            http_response_code(HttpStatus::BAD_REQUEST);
//            echo json_encode(["success" => false, "message" => "Stan aktywności użytkownika $userId już wynosi $newActivity"]);
//            return;
//        }
//
//        // Aktualizujemy aktywnosc
//        $query = <<<SQL
//        UPDATE
//            users
//        SET is_active = ?
//        WHERE user_id = ?;
//        SQL;
//        $stmt = $conn->prepare($query);
//        $stmt->bind_param("ii", $newActivity, $userId);
//        $stmt->execute();
//
//        if ($stmt->affected_rows > 0) {
//            http_response_code(HttpStatus::OK);
//            $newActivityTxt = $newActivity === 1 ? "Aktywne" : "Nieaktywne";
//            echo json_encode(["success" => true, "message" => "Zmieniono aktywność konta użytkownika $userId na \"$newActivityTxt\""]);
//        }
//        else {
//            http_response_code(HttpStatus::NOT_FOUND);
//            echo json_encode(["success" => false, "message" => "Nie ma użytkownika o id $userId"]);
//        }
//        $conn->commit();
//    }
//    catch (Exception $e) {
//        $conn->rollback();
//        http_response_code(HttpStatus::INTERNAL_SERVER_ERROR);
//        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
//    }
//    finally {
//        $stmt?->close();
//        $conn?->close();
//    }
//}
//
//// Funkcja do edytowania danych uzytkownika
//function editUser(object $inputData): void {
//    $query = <<<SQL
//    UPDATE
//        users
//    SET
//        username = ?,
//        email = ?,
//        role_id = ?
//    SQL;
//
//    if (isset($inputData->aboutMe)) {
//        $query .= ", about_me = ?";
//    }
//
//    if (isset($inputData->password)) {
//        $hashedPassword = password_hash($inputData->password, PASSWORD_DEFAULT);
//        $query .= ", password = ?";
//    }
//    $query .= " WHERE user_id = ?";
//
//    $conn = createMySQLiConnection();
//    try {
//        $conn->begin_transaction();
//
//        $stmt = $conn->prepare($query);
//        if (isset($inputData->password)) {
//            if (isset($inputData->aboutMe)) {
//                $stmt->bind_param(
//                    "ssissi",
//                    $inputData->username,
//                    $inputData->email,
//                    $inputData->role,
//                    $inputData->aboutMe,
//                    $hashedPassword,
//                    $inputData->id
//                );
//            } // if isset aboutMe
//            else {
//                $stmt->bind_param(
//                    "ssisi",
//                    $inputData->username,
//                    $inputData->email,
//                    $inputData->role,
//                    $hashedPassword,
//                    $inputData->id
//                );
//            } // else isset aboutMe
//        } // if isset password
//        else {
//            if (isset($inputData->aboutMe)) {
//                $stmt->bind_param(
//                    "ssisi",
//                    $inputData->username,
//                    $inputData->email,
//                    $inputData->role,
//                    $inputData->aboutMe,
//                    $inputData->id
//                );
//            } // if isset aboutMe
//            else {
//                $stmt->bind_param(
//                    "ssii",
//                    $inputData->username,
//                    $inputData->email,
//                    $inputData->role,
//                    $inputData->id
//                );
//            } // else isset aboutMe
//        } // else isset password
//
//        $stmt->execute();
//
//        if ($stmt->affected_rows > 0) {
//            http_response_code(HttpStatus::OK);
//            echo json_encode(["success" => true, "message" => "Zaktualizowano użytkownika o id " . $inputData->id]);
//        }
//        else {
//            http_response_code(HttpStatus::NOT_FOUND);
//            echo json_encode(["success" => false, "message" => "Brak aktualizacji"]);
//        }
//
//        $conn->commit();
//    }
//    catch (Exception $e) {
//        $conn->rollback();
//        http_response_code(HttpStatus::INTERNAL_SERVER_ERROR);
//        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
//    }
//    finally {
//        $stmt?->close();
//        $conn?->close();
//    }
//}
//
//// Funkcja do usuwania uzytkownika, posta lub komentarza
//function deleteContent(string $type, int $id): void {
//    $conn = null;
//    $stmt = null;
//    try {
//        $conn = createMySQLiConnection();
//
//        $query = match ($type) {
//            "post" => "DELETE FROM posts WHERE post_id = ?",
//            "user" => "DELETE FROM users WHERE user_id = ?",
//            "comment" => "DELETE FROM comments WHERE comment_id = ?",
//            default => throw new Exception("Invalid type parameter"),
//        };
//
//        $conn->begin_transaction();
//
//        $stmt = $conn->prepare($query);
//        $stmt->bind_param("i", $id);
//        $stmt->execute();
//
//        if ($stmt->affected_rows > 0) {
//            http_response_code(HttpStatus::OK);
//            echo json_encode(["success" => true, "message" => "Usunięto {$type} o id $id"]);
//        }
//        else {
//            http_response_code(HttpStatus::NOT_FOUND); // Not Found
//            echo json_encode(["success" => false, "message" => "Nie ma {$type} o id $id"]);
//        }
//        $conn->commit();
//    }
//    catch (Exception $e) {
//        $conn->rollback();
//        http_response_code(HttpStatus::INTERNAL_SERVER_ERROR);
//        echo json_encode(["success" => false, "message" => "Error: " . $e->getMessage()]);
//    }
//    finally {
//        $stmt?->close();
//        $conn?->close();
//    }
//}