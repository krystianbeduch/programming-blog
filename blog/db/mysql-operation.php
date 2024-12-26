<?php

use JetBrains\PhpStorm\NoReturn;

require_once "db-connect.php";
require_once "user-management.php";
require_once "posts-management.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Sprawdzanie, czy sesja jest juz aktywna
    }

    $action = $_POST["action"] ?? null;

    match ($action) {
        // Wyslanie komentarza do bazy
        "addComment" => addCommentToPost($_POST),
        "addPost" => addPost($_POST),
        "editForm" => correctForm(),
        "registerUser" => createUserAccount($_POST),
        "loginUser" => loginUser($_POST),
        "editUserAccount" => editUserAccount($_POST),
        "editPost" => editPost($_POST),
        default => handleUnknownAction($action),
    };
}

#[NoReturn]
function correctForm(): void {
    header("Location: " . $_POST["url"]);
    exit();
}

#[NoReturn]
function handleUnknownAction(?string $action): void {
    echo "Nieznana akcja: " . htmlspecialchars($action);
    exit();
}

function createMySQLiConnection() : mysqli {
    return new mysqli(
        MySQLConfig::SERVER,
        MySQLConfig::USER,
        MySQLConfig::PASSWORD,
        MySQLConfig::DATABASE
    );
}

#[NoReturn]
function handleDatabaseError(Exception $e, ?string $headerLocation = null): void {
    $errorMessage = $e instanceof mysqli_sql_exception
        ? "Problem połączenia z bazą: " . $e->getMessage()
        : "Błąd: " . $e->getMessage();

    $_SESSION["alert"]["error"] = $errorMessage;
    $redirectLocation = $headerLocation ?? "../pages/index.php";

    header("Location: $redirectLocation");
    exit();
}

/**
 * @param array $user
 * @param mysqli $conn
 * @param array $setParts
 * @return string
 * @throws Exception
 */
function getSetClause(array $user, mysqli $conn, array $setParts): string {
    foreach ($user as $field => $value) {
        if ($field == "action" || $field == "attachment-id") {
            continue;
        }
        $escapedField = $conn->real_escape_string($field);
        $escapedValue = $conn->real_escape_string($value);
        $setParts[] = "`$escapedField` = '$escapedValue'";
    }

    if (empty($setParts) && !$user["attachment-id"]) {
        throw new Exception("Brak danych do aktualizacji");
    }

    return implode(", ", $setParts);
} // getSetClause()