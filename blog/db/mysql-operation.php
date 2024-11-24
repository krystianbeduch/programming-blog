<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"] ?? null;

    match ($action) {
        // Wyslanie komentarza do bazy
        "addComment" => addCommentToMySQLDataBase($_POST),
        default => handleUnknownAction($action),
    };
}

function handleUnknownAction(?string $action): void {
    echo "Nieznana akcja: " . htmlspecialchars($action);
}


function getPosts(string $category) : array {
    try {
        $conn = new mysqli("localhost", "root", "", "blog");
        $query = 'SELECT category_id FROM categories WHERE LOWER(category_name) = LOWER("' . $category .'");';
        $result = $conn->query($query);
        $categoryId = $result->fetch_assoc()['category_id'];
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
        $conn = new mysqli("localhost", "root", "", "blog");
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
        $conn = new mysqli("localhost", "root", "", "blog");
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
//        $postId = $_POST['post-id'];
//        $nick = $_POST['nick'];
//        $email = $_POST['email'];
//        $comment = $_POST['comment'];
        $conn = new mysqli("localhost", "root", "", "blog");
//        $query = "INSERT INTO comments (user_id, nickname, nickname, email, content, created_at) VALUES ('${topic}', '${nick}', '${email}', '${comment}')";

        $query = $conn->prepare("INSERT INTO comments (user_id, nickname, email, content, created_at, post_id) VALUES (null, ?, ?, ?, NOW(), ?)");
        $query->bind_param(
            "sssi",
            $commentData['nick'],
            $commentData['email'],
            $commentData['comment'],
            $commentData['post-id']
        );
        $query->execute();
        $query->close();
        $conn->close();
////    echo $query;
//        $result = $conn->query($query);
////    echo $result;
//
//        $query = "SELECT * FROM posts";
//        $result = $conn->query($query);
//        while ($row = $result->fetch_assoc()) {
//            echo $row['topic'] . " ";
//            echo $row['nick'] . " ";
//            echo $row['email'] . " ";
//            echo $row['comment'] . " ";
//            echo "<br>";
//        }
//        $conn->close();
    }
    catch (mysqli_sql_exception $e) {
        echo "Błąd połączenia z bazą: ".$e->getMessage();
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
    }
}

function checkCategory(string $language) : bool {
    $conn = new mysqli("localhost", "root", "", "blog");
    $query = "SELECT 1 FROM categories WHERE LOWER(category_name) = LOWER('" . $language . "');";
    $result = $conn->query($query);
    $conn->close();
    return $result->num_rows > 0;
}

?>

