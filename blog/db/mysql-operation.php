<?php
use JetBrains\PhpStorm\NoReturn;

require_once "db-connect.php";
require_once "user-management.php";
require_once "posts-management.php";

#[NoReturn]
function correctForm(): void {
    header("Location: " . $_POST["url"]);
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