<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Origin: *");

require_once "../mysql-operation.php";
require_once "../../errors/error-codes.php";

$method = $_SERVER["REQUEST_METHOD"];
switch ($method) {
    case "POST":
        addUserScore();
        break;
    case "GET":
        if (isset($_GET["getUserName"])) {
            getSessionUserName();
        }
        else {
            getUserScores();
        }
        break;
    default:
        http_response_code(HttpStatus::METHOD_NOT_ALLOWED);
        echo json_encode(["message" => "Method not allowed"]);
        exit();
} // switch

function getSessionUserName(): void  {
    $conn = null;
    $stmt = null;
    try {
        session_start();

        if (empty($_SESSION["loggedUser"]["username"])) {
            http_response_code(HttpStatus::NOT_FOUND);
            echo json_encode(["message" => "User not logged in"]);
            exit();
        }

        $username = $_SESSION["loggedUser"]["username"];

        $conn = createMySQLiConnection();

        $query = <<<SQL
        SELECT 
            username 
        FROM users 
        WHERE username = ?;
        SQL;

        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_object()) {
            echo json_encode(["username" => $row->username]);
        }
        else {
            http_response_code(HttpStatus::NOT_FOUND);
            echo json_encode(["message" => "Username not found"]);
        }
    }
    catch (Exception $e) {
        http_response_code(HttpStatus::INTERNAL_SERVER_ERROR);
        echo json_encode(["message" => $e->getMessage()]);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
} // getSessionUserName()

function getUserScores() : void {
    $conn = null;
    try {
        $conn = createMySQLiConnection();
        $query = <<<SQL
        SELECT 
            user_name, 
            score 
        FROM snake_scores 
        ORDER BY score DESC LIMIT 10;
        SQL;
        $result = $conn->query($query);
        echo json_encode(["success" => true, "scores" => $result->fetch_all(MYSQLI_ASSOC)]);
    }
    catch (Exception $e) {
        http_response_code(HttpStatus::INTERNAL_SERVER_ERROR);
        echo json_encode(["success" => false, "message" => "Error: ". $e->getMessage()]);
    }
    finally {
        $conn?->close();
    }
} // getUserScores()

function addUserScore() : void {
    $conn = null;
    $stmt = null;
    try {
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data->username) || !isset($data->score)) {
            http_response_code(HttpStatus::BAD_REQUEST);
            echo json_encode(["success" => false, "message" => "Invalid type parameter"]);
            exit();
        }

        $conn = createMySQLiConnection();
        $query = <<<SQL
        INSERT INTO snake_scores 
            (user_name, score) 
        VALUES 
            (?, ?);
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "si",
            $data->username,
            $data->score
        );
        $stmt->execute();
        echo json_encode(["success" => true, "message" => "Zapisano wynik do bazy"]);
    }
    catch (Exception $e) {
        http_response_code(HttpStatus::INTERNAL_SERVER_ERROR);
        echo json_encode(["success" => false, "message" => "Error: ". $e->getMessage()]);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
} // addUserScore()