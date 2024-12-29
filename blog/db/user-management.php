<?php

require_once "db-connect.php";
require_once "mysql-operation.php";

function getUserRole(string $roleName) : int {
    $roleId = -1;
    $conn = null;
    $stmt = null;
    try {
        $conn = createMySQLiConnection();
        $query = <<<SQL
        SELECT 
            role_id 
        FROM roles 
        WHERE role_name = ?;
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $roleName);
        $stmt->execute();
        $roleId = $stmt->get_result()->fetch_object()->role_id ?: -1;
    }
    catch (mysqli_sql_exception|Exception $e) {
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
    return $roleId;
} // getUserRole()


function createUserAccount(array $user) : void {
    $conn = null;
    $stmt = null;
    try {
        $roleId = getUserRole($user["role"]);
        if ($roleId == -1) {
            throw new Exception("Nieznana rola");
        }
        $conn = createMySQLiConnection();
        $password = password_hash($user["password"], PASSWORD_DEFAULT);
        $conn->begin_transaction();
        $query = <<<SQL
        INSERT INTO users 
            (username, email, password, about_me, role_id) 
        VALUES 
            (?, ?, ?, ?, ?)
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssssi",
            $user["username"],
            $user["email"],
            $password,
            $user["about"],
            $roleId
        );
        $stmt->execute();
        $conn->commit();

        $_SESSION["alert"]["successStrong"] = "Zarejestrowano!";
        $_SESSION["alert"]["success"] = "Poczekaj na aktywację konta przez administratora.";

        // Powrot na strone z ktorej nastapila rejestracja
        $redirectUrl = $_SERVER["HTTP_REFERER"] ?? "../pages/";
        header("Location: $redirectUrl");
    }
    catch (mysqli_sql_exception|Exception $e) {
        $conn->rollback();
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
} // createUserAccount()

function loginUser(array $user) : void {
    $conn = null;
    $stmt = null;
    try {
        $conn = createMySQLiConnection();
        $query = <<<SQL
        SELECT 
            u.user_id, 
            u.username, 
            u.password, 
            u.email, 
            u.about_me, 
            u.is_active, 
            r.role_name 
        FROM users u 
        JOIN roles r ON u.role_id = r.role_id 
        WHERE username = ?;
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s",$user["username"]);
        $stmt->execute();
        $userData = $stmt->get_result()->fetch_object();

        if ($userData->user_id && password_verify($user["password"], $userData->password)) {
            if ($userData->is_active == 1) {
                $_SESSION["loggedUser"] = [
                    "id" => $userData->user_id,
                    "username" => $userData->username,
                    "email" => $userData->email,
                    "aboutMe" => $userData->about_me,
                    "role" => $userData->role_name,
                ];

                $_SESSION["alert"]["successStrong"] = "Zalogowano!";
                $_SESSION["alert"]["success"] = "Witaj " . $userData->username;
            }
            else {
                $_SESSION["alert"]["error"] = "Konto nie aktywne.<br>Poczekaj aż administrator aktywuje konto.";
            }
        }
        else {
            $_SESSION["alert"]["error"] = "Nieprawidłowe hasło";
        }

        // Powrot na strone z ktorej nastapilo logowanie
        $redirectUrl = $_SERVER["HTTP_REFERER"] ?? "../pages/";
        header("Location: $redirectUrl");
    }
    catch (mysqli_sql_exception|Exception $e) {
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
} // loginUser()

function editUserAccount(array $user) : void {
    $conn = null;
    $stmt = null;
    $headerLocation = "../pages/edit-profile.php";
    try {
        $conn = createMySQLiConnection();
        $conn->begin_transaction();

        // Jesli nie ma id uzytkownika - blad
        if (!isset($user["id"])) {
            throw new Exception("Brak identyfikatora użytkownika");
        }
        $userId = (int) $user["id"];
        unset($user["id"]); // Nie aktualizujemy ID uzytkownika

        $setParts = [];

        if (isset($user["current-password"], $user["new-password"], $user["new-password-confirm"])) {
            // Pobierz biezace haslo uzytkownika
            $query = <<<SQL
            SELECT
                password
            FROM users
            WHERE user_id = ?
            SQL;

            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->bind_result($currentPasswordHash);
            $stmt->fetch();
            $stmt->close();

            // Sprawdz, czy biezace haslo sie zgadza
            if (!password_verify($user["current-password"], $currentPasswordHash)) {
                throw new Exception("Obecne hasło nie prawidłowe");
            }

            // Sprawdz, czy nowe hasla są zgodne
            if ($user["new-password"] != $user["new-password-confirm"]) {
                throw new Exception("Nowe hasła nie są zgodne");
            }

            // Hashowanie nowego hasla
            $hashedPassword = password_hash($user["new-password"], PASSWORD_DEFAULT);
            $setParts[] = "`password` = '$hashedPassword'";
        }
        // Usuwamy pola zwiazane z haslem, aby nie zostaly dodane do innych pol
        unset($user["current-password"], $user["new-password"], $user["new-password-confirm"]);

        // Przygotowanie zapytania UPDATE
        $setClause = getSetClause($user, $conn, $setParts);
        $query = <<<SQL
        UPDATE 
            users 
        SET $setClause 
        WHERE user_id = $userId;
        SQL;

        // Wykonujemy zapytanie
        $conn->query($query);
        $conn->commit();

        if (isset($_SESSION["loggedUser"])) {
            unset($_SESSION["loggedUser"]);
        }

        $_SESSION["alert"]["successStrong"] = "Zapisano zmiany!";
        $_SESSION["alert"]["success"] = "Zaloguj się ponownie";
        header("Location: ../pages/index.php");
    }
    catch (mysqli_sql_exception|Exception $e) {
        $conn->rollback();
        handleDatabaseError($e, $headerLocation);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
} // editUserAccount()

function getUsers_Admin() : array {
    $users = [];
    $conn = null;
    $stmt = null;
    try {
        $conn = createMySQLiConnection();
        $query = <<<SQL
        SELECT 
            u.user_id, 
            u.username, 
            u.email, 
            u.about_me, 
            COUNT(p.user_id) AS 'posts_count', 
            u.created_at, 
            u.updated_at, 
            u.is_active, 
            r.role_name 
        FROM users u 
        JOIN roles r ON u.role_id = r.role_id 
        LEFT JOIN posts p ON u.user_id = p.user_id 
        GROUP BY 
            u.user_id, 
            u.username, 
            u.email, 
            u.about_me, 
            u.created_at, 
            u.updated_at, 
            u.is_active, 
            r.role_name 
        ORDER BY 1;
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?? [];
    }
    catch (mysqli_sql_exception|Exception $e) {
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
    return $users;
} // getUsers_Admin()