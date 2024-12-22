<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Origin: *");

require_once "../db-connect.php";

$method = $_SERVER["REQUEST_METHOD"];
if ($method == "GET" && isset($_GET["getUserName"])) {
    getSessionUserName();
}
else if ($method == "GET") {
    getUserScores();
}
else if ($method == "POST") {
    addUserScore();
}
else {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
}

function getSessionUserName(): void  {
    try {
        session_start();
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        if (isset($_SESSION["loggedUser"]["username"])) {
            $username = $_SESSION["loggedUser"]["username"];
        }
        else {
            http_response_code(404); // Not Found
            exit;
        }

        $query = "SELECT username FROM users WHERE username = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        echo json_encode(["username" => $result->fetch_assoc()["username"]]);
    }
    catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["message" => $e->getMessage()]);
    }
}

function getUserScores() : void {
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = "SELECT user_name, score FROM snake_scores ORDER BY score DESC LIMIT 10";
        $result = $conn->query($query);
        $conn->close();
        echo json_encode(["success" => true, "scores" => $result->fetch_all(MYSQLI_ASSOC)]);
    }
    catch (Exception $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["success" => false, "message" => "Error: ". $e->getMessage()]);
    }
}

function addUserScore() : void {
    try {
        $data = json_decode(file_get_contents("php://input"), true);

        if (isset($data["username"]) && isset($data["score"])) {
            $conn = new mysqli(
                MySQLConfig::SERVER,
                MySQLConfig::USER,
                MySQLConfig::PASSWORD,
                MySQLConfig::DATABASE
            );
            $query = "INSERT INTO snake_scores (user_name, score) VALUES (?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param(
                "si",
                $data["username"],
                $data["score"]
            );
            $stmt->execute();
            $stmt->close();
            $conn->close();
            echo json_encode(["success" => true, "message" => "Zapisano wynik do bazy"]);
        }
        else {
            http_response_code(400); // Bad Request
            echo json_encode(["success" => false, "message" => "Invalid type parameter"]);
        }
    }
    catch (Exception $e) {
        echo json_encode(["success" => false, "message" => "Error: ". $e->getMessage()]);
    }
}