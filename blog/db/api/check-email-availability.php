<?php
// informacja dla klienta (np. przegladarki), ze odpowiedz bedzie w formacie JSON
header("Content-Type: application/json");
require_once "../db-connect.php";

try {
    // Sprawdź, czy parametr 'username' został przekazany
    if (!isset($_GET["email"])) {
        echo json_encode(["success" => false, "message" => "Brak emailu"]);
        http_response_code(400); // Błąd - brak parametru
        exit;
    }

    $conn = new mysqli(
        MySQLConfig::SERVER,
        MySQLConfig::USER,
        MySQLConfig::PASSWORD,
        MySQLConfig::DATABASE
    );


    // Pobranie emaila z parametru URL
    $email = $_GET["email"];

    // Przygotowanie zapytania SQL
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    $conn->close();

    // Zwrócenie wyniku w formacie JSON
    if ($count > 0) {
        echo json_encode(["success" => false, "message" => "Email o takiej nazwie istnieje"]);
    }
    else {
        echo json_encode(["success" => true, "message" => "Email wolny"]);
    }


//    echo json_encode(["available" => $count == 0]);
//
//    echo $result->fetch_row()[0];
//    echo json_encode(["available" => $result->fetch_row()[0] == 0]);
}
catch (Exception $e) {
    http_response_code(500);
//    echo json_encode(["error" => "Blad polaczenia z baza"]);
    echo json_encode(["success" => false, "message" => "Blad: {$e}"]);
    exit;
}



?>
