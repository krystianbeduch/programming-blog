<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require_once "../db-connect.php";

try {
    $data = json_decode(file_get_contents("php://input"), true);
    if (isset($data["type"]) && $data["type"] == "save") {
        if (isset($data["userName"]) && isset($data["score"])) {
            $conn = new mysqli(
                MySQLConfig::SERVER,
                MySQLConfig::USER,
                MySQLConfig::PASSWORD,
                MySQLConfig::DATABASE
            );
            $userName = $data["userName"];
            $score = $data["score"];
            $query = "INSERT INTO snake_scores (user_name, score) VALUES ('$userName', '$score')";
            $conn->query($query);
            $conn->close();
            echo json_encode(["message" => "Zapisano wynik do bazy"]);
        }
    }
    else if (isset($data["type"]) && $data["type"] == "get") {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
//        $userName = $data["userName"];
//        $score = $data["score"];
        $query = "SELECT * FROM snake_scores";
        $result = $conn->query($query);
        $conn->close();
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    }
}
catch (mysqli_sql_exception $e) {
    echo "Błąd połączenia z bazą: ".$e->getMessage();
    exit;
}
catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
    exit;
}