<?php
// Ustaw nagłówki CORS i typ odpowiedzi
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Origin: *");

require_once "../db-connect.php";

try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = "SELECT user_name, score FROM snake_scores ORDER BY score DESC";
        $result = $conn->query($query);
        $conn->close();
        echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}
catch (mysqli_sql_exception $e) {
    echo "Błąd połączenia z bazą: ".$e->getMessage();
    exit;
}
catch (Exception $e) {
    echo "Błąd: " . $e->getMessage();
    exit;
}