<?php
require_once "db-connect.php";
//session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? null;

    match ($action) {
        // Wyslanie komentarza do bazy
        "addComment" => addCommentToPost($_POST),
        "addPost" => addPost($_POST),
        "editForm" => correctForm(),
        "registerUser" => createUserAccount($_POST),
        "loginUser" => loginUser($_POST),
        "editUserAccount" => editUserAccount($_POST),
        default => handleUnknownAction($action),
    };
}

function correctForm(): void {
    header("Location: " . $_POST["url"]);
}

function handleUnknownAction(?string $action): void {
    echo "Nieznana akcja: " . htmlspecialchars($action);
}

function getCategoryId(string $category) : int {
    $categoryId = -1; // Domyslna wartosc w przypadku bledu lub braku rezultatu
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $stmt = $conn->prepare("SELECT category_id FROM categories WHERE category_name = ?;");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $stmt->bind_result($fetchedCategoryId);
        if ($stmt->fetch()) {
            $categoryId = $fetchedCategoryId;
        }
    }
    catch (mysqli_sql_exception $e) {
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: ".$e->getMessage();
        header("Location: index.php");
        exit;
    }
    catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: index.php");
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
        return $categoryId;
    }
}

function getPosts(string $category) : array {
    $posts = [];
    try {
        $categoryId = getCategoryId($category);
        if ($categoryId == -1) {
            throw new Exception("Nie znana kategoria");
        }

        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = "SELECT 
                    p.post_id, p.title, p.content, p.created_at, p.updated_at, p.is_published, 
                    u.username, u.email 
                FROM posts p JOIN users u ON p.user_id = u.user_id 
                WHERE p.category_id = ? ORDER BY p.created_at DESC;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $posts = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception $e) {
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: index.php");
        exit;
    }
    catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: index.php");
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
        return $posts;
    }
}

function getOnePost(int $postId): array {
    $post = [];
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = "SELECT 
                    p.post_id, p.title, p.content, p.created_at, p.updated_at, p.is_published, 
                    u.username, u.email, u.about_me 
                FROM posts p JOIN users u ON p.user_id = u.user_id 
                WHERE p.post_id = ? ORDER BY p.created_at DESC;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $post = $result->fetch_assoc();
        }
    }
    catch (mysqli_sql_exception $e) {
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: index.php");
        exit;
    }
    catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: index.php");
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
        return $post;
    }
}

function getOnePostToEdit(int $userId, int $postId): array {
    $post = [];
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = "SELECT post_id, title, content, LOWER(category_name) AS 'category_name' 
                    FROM posts p JOIN categories ca ON p.category_id = ca.category_id 
                    WHERE p.user_id = ? AND post_id = ?;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $userId, $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $post = $result->fetch_assoc();
        }
    }
    catch (mysqli_sql_exception $e) {
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: index.php");
        exit;
    }
    catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: index.php");
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
        return $post;
    }
}



function getCommentsToPost(int $postId) : array {
    $comments = [];
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = "SELECT 
                    c.post_id, u.user_id, IFNULL(u.username, c.username) AS username, 
                    IFNULL(u.email, c.email) AS email, c.created_at, c.content 
                FROM comments c LEFT JOIN users u ON c.user_id = u.user_id 
                WHERE post_id = ? ORDER BY c.created_at DESC;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $comments = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception $e) {
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: index.php");
        exit;
    }
    catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: index.php");
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
        return $comments;
    }
}

function addCommentToPost(array $commentData) : void {
    session_start();
    $postId = $commentData["post-id"];
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        // Rozpoczecie transakcji
        $conn->begin_transaction();

        // Przygotowanie i wykonanie zapytania
        $stmt = $conn->prepare(
            "INSERT INTO comments
                    (user_id, username, email, content, created_at, post_id) 
                    VALUES (null, ?, ?, ?, NOW(), ?)");
        $stmt->bind_param(
            "sssi",
            $commentData["username"],
            $commentData["email"],
            $commentData["content"],
            $postId
        );
        $stmt->execute();

        if ($stmt->affected_rows == 0) {
            throw new Exception("Nie udało się dodać komentarza");
        }

        if ($_SESSION["formData"][$postId]) {
            unset($_SESSION["formData"][$postId]);
        }

        $_SESSION["addCommentAlert"]["result"] = true;

        // Zatwierdzenie transkacji
        $conn->commit();
    }
    catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $_SESSION["addCommentAlert"]["result"] = false;
        $_SESSION["addCommentAlert"]["error"] = "Błąd połączenia z bazą: ".$e->getMessage();
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
    }
    finally {
        $stmt?->close();
        $conn?->close();
        header("Location: ../pages/post.php?postId=" . $postId);
    }
}

function checkCategory(string $language) : bool {
    $result = false;
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $stmt = $conn->prepare("SELECT 1 FROM categories WHERE category_name = ?;");
        $stmt->bind_param("s", $language);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $result = true;
        }
    }
    catch (mysqli_sql_exception $e) {
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: index.php");
        exit;
    }
    catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: index.php");
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
        return $result;
    }
}

function addPost(array $postData) : void {
    session_start();
    $conn = null;
    $stmt = null;
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $conn->begin_transaction();
        $stmt = $conn->prepare(
            "INSERT INTO posts 
                    (title, content, created_at, updated_at, is_published, user_id, category_id) 
                    VALUES (?, ?, NOW(), NOW(), ?, ?, ?)");
        $publish = 1;
        $categoryId = getCategoryId($postData["category"]);
        $stmt->bind_param(
            "ssiii",
            $postData["title"],
            $postData["content"],
            $publish,
            $postData["user-id"],
            $categoryId
        );
        $stmt->execute();

        if (isset($_SESSION["formData"][$postData["category"]])) {
            unset($_SESSION["formData"][$postData["category"]]);
        }

        $_SESSION["addPostAlert"]["result"] = true;
        $conn->commit();
    }
    catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $_SESSION["addPostAlert"]["result"] = false;
        $_SESSION["addPostAlert"]["error"] = "Błąd połączenia z bazą: ".$e->getMessage();
        echo "sd";
    }
    catch (Exception $e) {
        $conn->rollback();
        echo "Błąd: " . $e->getMessage();
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
        header("Location: ../pages/" . $postData["category"] . ".php");
    }
}

function getUserRole(string $roleName) : int {
    $roleId = -1;
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $stmt = $conn->prepare("SELECT role_id FROM roles WHERE role_name = ?;");
        $stmt->bind_param("s", $roleName);
        $stmt->execute();
        $stmt->bind_result($fetchedRoleId);
        $stmt->fetch();
        if ($fetchedRoleId) {
            $roleId = $fetchedRoleId;
        }
    }
    catch (mysqli_sql_exception $e) {
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: index.php");
        exit;
    }
    catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: index.php");
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
        return $roleId;
    }
}


function createUserAccount(array $user) : void {
    $conn = null;
    $stmt = null;
    try {
        $roleId = getUserRole($user["role"]);
        if ($roleId == -1) {
            throw new Exception("Nieznana rola");
        }
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $password = password_hash($user["password"], PASSWORD_DEFAULT);
        $conn->begin_transaction();
        $query = "INSERT INTO 
                    users (username , email, password, created_at, about_me, role_id) 
                    VALUES (?, ?, ?, NOW(), ?, ?);";
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
        loginUser($user);
    }
    catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: ../pages/index.php");
        exit;
    }
    catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: ../pages/index.php");
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
    }
}

function loginUser(array $user) : void {
    try {
        session_start();
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $stmt = $conn->prepare("SELECT user_id, username, password, email, about_me 
                                        FROM users WHERE username = ?");
        $stmt->bind_param("s",$user["username"]);
        $stmt->execute();
        $stmt->bind_result($userId, $username, $hashedPassword, $email, $aboutMe);
        $stmt->fetch();

        if ($userId !== null && password_verify($user["password"], $hashedPassword)) {
            $_SESSION["loggedUser"]["id"] = $userId;
            $_SESSION["loggedUser"]["username"] = $username;
            $_SESSION["loggedUser"]["email"] = $email;
            $_SESSION["loggedUser"]["aboutMe"] = $aboutMe;

            $_SESSION["loginAlert"] = ["type" => "success"];
        }
        else {
            $_SESSION["loginAlert"] = ["type" => "danger"];
        }

        // Powrot na strone z ktorej nastapilo logowanie
        $redirectUrl = $_SERVER["HTTP_REFERER"] ?? "../pages/";
        header("Location: $redirectUrl");

    }
    catch (mysqli_sql_exception $e) {
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: ../pages/index.php");
        exit;
    }
    catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: ../pages/index.php");
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
    }
}

function editUserAccount(array $user) : void {
    $conn = null;
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );

        // Jesli nie ma id uzytkownika - blad
        if (!isset($user["id"])) {
            throw new Exception("Brak identyfikatora użytkownika");
        }
        $userId = (int) $user["id"];
        unset($user["id"]); // Nie aktualizujemy ID uzytkownika

        $setParts = [];

        if (isset($user["current-password"], $user["new-password"], $user["new-password-confirm"])) {
            // Pobierz biezace haslo uzytkownika
            $stmt = $conn->prepare("SELECT password FROM users WHERE user_id = ?");
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

        foreach ($user as $field => $value) {
            if ($field == "action") {
                continue; // Pomijamy pole "action"
            }
            $escapedField = $conn->real_escape_string($field);
            $escapedValue = $conn->real_escape_string($value);
            $setParts[] = "`$escapedField` = '$escapedValue'";
        }

        if (empty($setParts)) {
            throw new Exception("Brak danych do aktualizacji");
        }

        $setClause = implode(", ", $setParts);
        $query = "UPDATE users SET $setClause WHERE user_id = $userId";

        // Wykonujemy zapytanie
        $conn->begin_transaction();
        $conn->query($query);
        $conn->commit();

        session_start();
        if (isset($_SESSION["loggedUser"])) {
            unset($_SESSION["loggedUser"]);
        }

        $_SESSION["editProfileAlert"] = true;
        header("Location: ../pages/index.php");
        exit;
    }
    catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
//        header("Location: ../pages/index.php");
//        exit;
    }
    catch (Exception $e) {
        $conn->rollback();
        $_SESSION["alert"]["error"] = $e->getMessage();
//        header("Location: ../pages/index.php");
//        exit;
    }
}

function getUserPosts(int $userId) : array {
//    $conn = null;
    $comments = [];
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE

        );
        $query = "    
                SELECT 
                    p.post_id, p.title, p.content, p.updated_at, ca.category_name, COUNT(c.comment_id) AS comment_count 
                FROM posts p LEFT JOIN comments c ON p.post_id = c.post_id JOIN categories ca ON p.category_id = ca.category_id 
                WHERE p.user_id = ? 
                GROUP BY p.post_id, p.title, p.content, p.updated_at, ca.category_name
                ORDER BY p.updated_at DESC, comment_count DESC;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $comments = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception $e) {
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: index.php");
        exit;
    }
    catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: index.php");
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
        return $comments;
    }
}