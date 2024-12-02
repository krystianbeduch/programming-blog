<?php
require_once "db-connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? null;

    match ($action) {
        // Wyslanie komentarza do bazy
        "addComment" => addCommentToPost($_POST),
        "addPost" => addPost($_POST),
        "registerUser" => createUserAccount($_POST),
        "editForm" => test(),
        "loginUser" => loginUser($_POST),
        default => handleUnknownAction($action),
    };
}

function test() {
    echo "";
}

function handleUnknownAction(?string $action): void {
    echo "Nieznana akcja: " . htmlspecialchars($action);
}

function getCategoryId(string $category) : int {
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
        $stmt->bind_result($categoryId);
        $stmt->fetch();
        $stmt->close();
        $conn->close();
        if ($categoryId != null) {
            return $categoryId;
        }
        return -1;
    }
    catch (mysqli_sql_exception $e) {
        echo "Błąd połączenia z bazą: ".$e->getMessage();
        exit;
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
        exit;
    }
}


function getPosts(string $category) : array {
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
        $query = "SELECT p.post_id, p.title, p.content, p.created_at, p.updated_at, p.is_published, u.username, u.email FROM posts p JOIN users u ON p.user_id = u.user_id WHERE p.category_id = ? ORDER BY p.created_at DESC;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $conn->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    catch (mysqli_sql_exception $e) {
        echo "Błąd połączenia z bazą: ".$e->getMessage();
        exit;
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
        exit;
    }
}

function getOnePost(int $postId): array {
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = "SELECT p.post_id, p.title, p.content, p.created_at, p.updated_at, p.is_published, u.username, u.email FROM posts p JOIN users u ON p.user_id = u.user_id WHERE p.post_id = ? ORDER BY p.created_at DESC;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $conn->close();
        return $result->fetch_assoc();
    }
    catch (mysqli_sql_exception $e) {
        echo "Błąd połączenia z bazą: ".$e->getMessage();
        exit;
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
        exit;
    }
}

function getCommentsToPost(int $postId) : array {
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = "SELECT c.post_id, u.user_id, IFNULL(u.username, c.username) AS username, IFNULL(u.email, c.email) AS email, c.created_at, c.content FROM comments c LEFT JOIN users u ON c.user_id = u.user_id WHERE post_id = ? ORDER BY c.created_at DESC;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $postId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $conn->close();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    catch (mysqli_sql_exception $e) {
        echo "Błąd połączenia z bazą: ".$e->getMessage();
        exit;
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
        exit;
    }
}

function addCommentToPost(array $commentData) : void {
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $stmt = $conn->prepare("INSERT INTO comments (user_id, username, email, content, created_at, post_id) VALUES (null, ?, ?, ?, NOW(), ?)");
        $stmt->bind_param(
            "sssi",
            $commentData["username"],
            $commentData["email"],
            $commentData["content"],
            $commentData["post-id"]
        );
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
    catch (mysqli_sql_exception $e) {
        echo "Błąd połączenia z bazą: ".$e->getMessage();
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
    }
}

function checkCategory(string $language) : bool {
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
        $stmt->close();
        $conn->close();
        return $result->num_rows > 0;
    }
    catch (mysqli_sql_exception $e) {
        echo "Błąd połączenia z bazą: ".$e->getMessage();
        exit;
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
        exit;
    }
}

function addPost(array $postData) : void {
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $stmt = $conn->prepare("INSERT INTO posts (title, content, created_at, updated_at, is_published, user_id, category_id) VALUES (?, ?, NOW(), NOW(), ?, ?, ?)");
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
        $stmt->close();
        $conn->close();
    }
    catch (mysqli_sql_exception $e) {
        echo "Błąd połączenia z bazą: ".$e->getMessage();
        exit;
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
        exit;
    }
}

function getUserRole(string $roleName) : int {
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
        $stmt->bind_result($roleId);
        $stmt->fetch();
        $stmt->close();
        $conn->close();
        if ($roleId !== null) {
            return $roleId;
        }
        return -1;
    }
    catch (mysqli_sql_exception $e) {
        echo "Błąd połączenia z bazą: ".$e->getMessage();
        exit;
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
        exit;
    }
}


function createUserAccount(array $user) : void {
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
        $query = "INSERT INTO users (username , email, password, created_at, role_id) VALUES (?, ?, ?, NOW(), ?);";
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "sssi",
            $user["username"],
            $user["email"],
            $password,
            $roleId
        );
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
    catch (mysqli_sql_exception $e) {
        echo "Błąd połączenia z bazą: ".$e->getMessage();
        exit;
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
        exit;
    }
}

function loginUser(array $user) : void {
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $stmt = $conn->prepare("SELECT user_id, username, password, email FROM users WHERE username = ?");
        $stmt->bind_param("s",$user["username"]);
        $stmt->execute();
        $stmt->bind_result($userId, $username, $hashedPassword, $email);
        $stmt->fetch();
        $stmt->close();
        $conn->close();
        if ($userId !== null && password_verify($user["password"], $hashedPassword)) {
            session_start();
            $_SESSION["loggedUser"]["id"] = $userId;
            $_SESSION["loggedUser"]["username"] = $username;
            $_SESSION["loggedUser"]["email"] = $email;
        }
        // Powrot na strone z ktorej nastapilo logowanie
        $redirectUrl = $_SERVER["HTTP_REFERER"] ?? "../pages/";
        header("Location: $redirectUrl");
    }
    catch (mysqli_sql_exception $e) {
        echo "Błąd połączenia z bazą: ".$e->getMessage();
        exit;
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
        exit;
    }
}
?>

