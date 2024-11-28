<?php
require_once "db-connect.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? null;

    match ($action) {
        // Wyslanie komentarza do bazy
        "addComment" => addCommentToMySQLDataBase($_POST),
        "addPost" => addPostToMySQLDataBase($_POST),
        "registerUser" => addUserToMySQLDataBase($_POST),
        default => handleUnknownAction($action),
    };
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
        $query = 'SELECT category_id FROM categories WHERE LOWER(category_name) = LOWER("' . $category .'");';
        $result = $conn->query($query);
        return $result->fetch_assoc()['category_id'];
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
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
//        $query = 'SELECT category_id FROM categories WHERE LOWER(category_name) = LOWER("' . $category .'");';
//        $result = $conn->query($query);
//        $categoryId = $result->fetch_assoc()['category_id'];
        $query = "SELECT p.post_id, p.title, p.content, p.created_at, p.updated_at, p.is_published, u.nickname, u.email FROM posts p JOIN users u ON p.user_id = u.user_id WHERE p.category_id = " . $categoryId . " ORDER BY p.created_at DESC;";
        $result = $conn->query($query);
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
        $query = "SELECT p.post_id, p.title, p.content, p.created_at, p.updated_at, p.is_published, u.nickname, u.email FROM posts p JOIN users u ON p.user_id = u.user_id WHERE p.post_id = " . $postId . " ORDER BY p.created_at DESC;";
        $result = $conn->query($query);
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
        $query = 'SELECT c.post_id, u.user_id, IFNULL(u.nickname, c.nickname) AS nickname, IFNULL(u.email, c.email) AS email, c.created_at, c.content FROM comments c LEFT JOIN users u ON c.user_id = u.user_id WHERE post_id = ' . $postId . ' ORDER BY c.created_at DESC;';
        $result = $conn->query($query);
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

function addCommentToMySQLDataBase(array $commentData) : void {
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = $conn->prepare("INSERT INTO comments (user_id, nickname, email, content, created_at, post_id) VALUES (null, ?, ?, ?, NOW(), ?)");
        $query->bind_param(
            "sssi",
            $commentData['nick'],
            $commentData['email'],
            $commentData['content'],
            $commentData['post-id']
        );
        $query->execute();
        $query->close();
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
        $query = "SELECT 1 FROM categories WHERE LOWER(category_name) = LOWER('" . $language . "');";
        $result = $conn->query($query);
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

function addPostToMySQLDataBase(array $postData) : void {
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = $conn->prepare("INSERT INTO posts (title, content, created_at, updated_at, is_published, user_id, category_id) VALUES (?, ?, NOW(), NOW(), ?, ?, ?)");
        $publish = 1;
        $categoryId = getCategoryId($postData['category']);
        $query->bind_param(
            "ssiii",
            $postData['title'],
            $postData['content'],
            $publish,
            $postData['user-id'],
            $categoryId
        );
        $query->execute();
        $query->close();
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
        $query = 'SELECT role_id FROM roles WHERE LOWER(role_name) = LOWER("' . $roleName .'");';
        $result = $conn->query($query);
        return $result->fetch_assoc()['role_id'];
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


function addUserToMySQLDataBase(array $user) : void {
    try {
        print_r($user);
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = $conn->prepare("INSERT INTO users (nickname , email, password, created_at, role_id) VALUES (?, ?, ?, NOW(), ?)");
        $password = password_hash($user['password'], PASSWORD_DEFAULT);
        $roleId = getUserRole($user['role']);
//        echo $password;

        $query->bind_param(
            "sssi",
            $user['nickname'],
            $user['email'],
            $password,
            $roleId
        );
//        echo "sas";
        $query->execute();
        $query->close();
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

?>

