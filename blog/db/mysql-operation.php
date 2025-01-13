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
        "editPost" => editPost($_POST),
        default => handleUnknownAction($action),
    };
}

function correctForm(): void {
    header("Location: " . $_POST["url"]);
}

function handleUnknownAction(?string $action): void {
    echo "Nieznana akcja: " . htmlspecialchars($action);
}

function getCategoryDescription(string $category): string {
    $conn = null;
    $description = "";
    try {
        $categoryId = getCategoryId($category);
        if ($categoryId == -1) {
            throw new Exception("Nieznana kategoria");
        }
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = <<<SQL
        SELECT 
            description 
        FROM categories 
        WHERE category_id = ?;
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $stmt->bind_result($fetchedDescription);
        $stmt->fetch();
        if ($fetchedDescription) {
            $description = $fetchedDescription;
        }
    } catch (mysqli_sql_exception $e) {
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: index.php");
        exit;
    } finally {
        $stmt->close();
        $conn->close();
        return $description;
    }
} // getCategoryDescription()

function getCategoryId(string $category) : int {
    $categoryId = -1; // Domyslna wartosc w przypadku bledu lub braku rezultatu
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = <<<SQL
        SELECT
            category_id
        FROM categories
        WHERE category_name = ?;
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $stmt->bind_result($fetchedCategoryId);
        if ($stmt->fetch()) {
            $categoryId = $fetchedCategoryId;
        }
    } // try
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
} // getCategoryId()

function getPosts(?string $category = null) : array {
    $posts = [];
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );

        $query = <<<SQL
        SELECT 
            p1.post_id,
            c2.category_name,
            p1.title, 
            p1.content,
            p1.created_at, 
            p1.updated_at, 
            u.username, 
            u.email,
            COUNT(c1.post_id) AS 'comments_count',
            p2.file_data, 
            p2.file_type 
        FROM posts p1 
        JOIN users u ON p1.user_id = u.user_id 
        LEFT JOIN posts_attachments p2 ON p1.attachment_id = p2.attachment_id
        LEFT JOIN comments c1 ON p1.post_id = c1.post_id
        JOIN categories c2 ON p1.category_id = c2.category_id
        SQL;

        if ($category) {
            $categoryId = getCategoryId($category);
            if ($categoryId == -1) {
                throw new Exception("Nieznana kategoria");
            }
            $query .= " WHERE p1.category_id = ? ";
        }
        $query .= " GROUP BY 
                    p1.post_id, 
                    c2.category_name,
                    p1.title, 
                    p1.content,
                    p1.created_at, 
                    p1.updated_at, 
                    u.username, 
                    u.email,
                    p2.file_data, 
                    p2.file_type
                    ORDER BY p1.updated_at DESC;";
//        else {
//            // Zapytanie dla wszystkich postow
//            $query = <<<SQL
//            SELECT
//                p1.post_id,
//                c2.category_name,
//                p1.title,
//                p1.content,
//                p1.created_at,
//                p1.updated_at,
//                u.username,
//                u.email,
//                COUNT(c1.post_id) AS 'comments_count',
//                p2.file_data,
//                p2.file_type
//            FROM posts p1
//            JOIN users u ON p1.user_id = u.user_id
//            LEFT JOIN posts_attachments p2 ON p1.attachment_id = p2.attachment_id
//            LEFT JOIN comments c1 ON p1.post_id = c1.post_id
//            JOIN categories c2 ON p1.category_id = c2.category_id
//            GROUP BY
//                p1.post_id,
//                p1.title,
//                p1.content,
//                p1.created_at,
//                p1.updated_at,
//                u.username,
//                u.email,
//                p2.file_data,
//                p2.file_type
//            ORDER BY p1.post_id DESC;
//            SQL;
//            $stmt = $conn->prepare($query);
//        }
//        echo $query;

        $stmt = $conn->prepare($query);
        if ($category) {
            $stmt->bind_param("i", $categoryId);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $posts = $result->fetch_all(MYSQLI_ASSOC);
        }
    }
    catch (mysqli_sql_exception $e) {
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
//        echo $e->getMessage();
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
        return $posts;
    }
} // getPosts()

function getOnePost(int $postId): array {
    $post = [];
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = <<<SQL
        SELECT 
            p.post_id,
            c.category_name,
            p.title,
            p.content,
            p.created_at,
            p.updated_at, 
            u.username,
            u.email,
            u.about_me,
            pa.file_data,
            pa.file_type 
        FROM posts p 
        JOIN users u ON p.user_id = u.user_id 
        LEFT JOIN posts_attachments pa ON p.attachment_id = pa.attachment_id
        JOIN categories c ON p.category_id = c.category_id
        WHERE p.post_id = ? 
        ORDER BY p.created_at DESC;
        SQL;
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
}// getOnePost()

function getOnePostToEdit(int $userId, int $postId): array {
    $post = [];
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = <<<SQL
        SELECT 
            p.post_id,
            p.title,
            p.content,
            LOWER(c.category_name) AS 'category_name',
            pa.attachment_id,
            pa.file_data,
            pa.file_type
        FROM posts p 
        JOIN categories c ON p.category_id = c.category_id 
        LEFT JOIN posts_attachments pa ON p.attachment_id = pa.attachment_id 
        WHERE p.user_id = ? AND p.post_id = ?;
        SQL;
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
} // getOnePostToEdit()

function getCommentsToPost(int $postId) : array {
    $comments = [];
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = <<<SQL
        SELECT
            c.comment_id,
            c.post_id,
            u.user_id,
            IFNULL(u.username, c.username) AS username,
            IFNULL(u.email, c.email) AS email,
            c.created_at,
            c.content
        FROM comments c 
        LEFT JOIN users u ON c.user_id = u.user_id 
        WHERE post_id = ? 
        ORDER BY c.created_at DESC;
        SQL;
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
} // getCommentsToPost()

function addCommentToPost(array $commentData) : void {
    session_start();
    $postId = $commentData["post-id"];
    $conn = null;
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
        $query = <<<SQL
        INSERT INTO commentss 
            (username, email, content, post_id)
        VALUES 
            (?, ?, ?, ?)
        SQL;

        $stmt = $conn->prepare($query);
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

        header("Location: ../pages/post.php?postId=" . $postId);
    }
    catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $_SESSION["addCommentAlert"]["result"] = false;
        $_SESSION["addCommentAlert"]["error"] = "Błąd połączenia z bazą: ".$e->getMessage();
        header("Location: ../pages/index.php");
    }
    catch (Exception $e) {
        echo "Błąd: " . $e->getMessage();
        header("Location: ../pages/index.php");
    }
    finally {
        $stmt?->close();
        $conn?->close();

    }
} // addCommentToPost()

function checkCategory(string $language) : bool {
    $result = false;
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = <<<SQL
        SELECT 
            1 
        FROM categories 
        WHERE category_name = ?;
        SQL;
        $stmt = $conn->prepare($query);
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
} // checkCategory()

function addPost(array $postData) : void {
    session_start();
    $conn = null;
    $stmt = null;
    try {
        $attachmentId = null;
        if (isset($_SESSION["uploaded_file"])) {
            $attachmentId = addAttachmentToPost($_SESSION["uploaded_file"]);
            unset($_SESSION["uploaded_file"]);
        }

        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $conn->begin_transaction();
        $query = <<<SQL
        INSERT INTO posts
            (title, content, user_id, category_id, attachment_id)
        VALUES 
            (?, ?, ?, ?, ?)
        SQL;

        $stmt = $conn->prepare($query);
        $categoryId = getCategoryId($postData["category"]);
        $stmt->bind_param(
            "ssiii",
            $postData["title"],
            $postData["content"],
            $postData["user-id"],
            $categoryId,
            $attachmentId
        );
        $stmt->execute();

        if (isset($_SESSION["formData"][$postData["category"]])) {
            unset($_SESSION["formData"][$postData["category"]]);
        }

        $_SESSION["addPostAlert"]["result"] = true;
        $conn->commit();
        header("Location: ../pages/" . $postData["category"] . ".php");
    }
    catch (mysqli_sql_exception|Exception $e) {
        $conn->rollback();
        $_SESSION["addPostAlert"]["result"] = false;
        $_SESSION["addPostAlert"]["error"] = "Błąd połączenia z bazą: ".$e->getMessage();
        header("Location: ../pages/" . $postData["category"] . ".php");
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
    }
} // addPost()

function addAttachmentToPost($attachmentData) : int {
    $fileName = $attachmentData["name"];
    $fileType = $attachmentData["type"];
    $fileSize = $attachmentData["size"];
    $fileContent = base64_decode($attachmentData["content"]);
    $conn = null;
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $conn->begin_transaction();
        $query = <<<SQL
        INSERT INTO posts_attachments
            (file_name, file_type, file_size, file_data)
        VALUES 
            (?, ?, ?, ?)
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->bind_param(
            "ssis",
            $fileName,
            $fileType,
            $fileSize,
            $fileContent
        );
        $stmt->execute();

        // Pobranie ID ostatnio wstawionego rekordu
        $insertedId = $conn->insert_id;

        $conn->commit();
    }
    catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: ../pages/index.php");
        exit;
    }
    catch (Exception $e) {
        $conn->rollback();
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: ../pages/index.php");
        exit;
    }
    finally {
        $stmt->close();
        $conn->close();
        return $insertedId;
    }
} // addAtachmentToPost()


function getUserRole(string $roleName) : int {
    $roleId = -1;
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = <<<SQL
        SELECT 
            role_id 
        FROM roles 
        WHERE role_name = ?;
        SQL;
        $stmt = $conn->prepare($query);
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
} // getUserRole()


function createUserAccount(array $user) : void {
    $conn = null;
    session_start();
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
        $_SESSION["registerAlert"] = true;
        // Powrot na strone z ktorej nastapila rejestracja
        $redirectUrl = $_SERVER["HTTP_REFERER"] ?? "../pages/";
        header("Location: $redirectUrl");
    }
    catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: ../pages/index.php");
        exit();
    }
    catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: ../pages/index.php");
        exit();
    }
    finally {
        $stmt->close();
        $conn->close();
    }
} // createUserAccount()

function loginUser(array $user) : void {
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
        WHERE username = ?
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s",$user["username"]);
        $stmt->execute();
        $stmt->bind_result($userId, $username, $hashedPassword, $email, $aboutMe, $isActive, $roleName);
        $stmt->fetch();

        if ($userId !== null && password_verify($user["password"], $hashedPassword)) {
            if ($isActive == 1) {
                $_SESSION["loggedUser"]["id"] = $userId;
                $_SESSION["loggedUser"]["username"] = $username;
                $_SESSION["loggedUser"]["email"] = $email;
                $_SESSION["loggedUser"]["aboutMe"] = $aboutMe;
                $_SESSION["loggedUser"]["role"] = $roleName;

                $_SESSION["loginAlert"]["success"] = true;
            }
            else {
                $_SESSION["loginAlert"]["success"] = false;
                $_SESSION["loginAlert"]["error"] = "Konto nie aktywne.<br>Poczekaj aż administrator aktywuje konto.";
            }
        }
        else {
            $_SESSION["loginAlert"]["success"] = false;
            $_SESSION["loginAlert"]["error"] = "Nieprawidłowe hasło";
        }

        // Powrot na strone z ktorej nastapilo logowanie
        $redirectUrl = $_SERVER["HTTP_REFERER"] ?? "../pages/";
        header("Location: $redirectUrl");
    }
    catch (mysqli_sql_exception $e) {
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: ../pages/index.php");
        exit();
    }
    catch (Exception $e) {
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: ../pages/index.php");
        exit();
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
} // loginUser()

function editUserAccount(array $user) : void {
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
//        $conn->close();

        if (isset($_SESSION["loggedUser"])) {
            unset($_SESSION["loggedUser"]);
        }

        $_SESSION["editProfileAlert"] = true;
        header("Location: ../pages/index.php");
//        exit();
    }
    catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        header("Location: ../pages/edit-profile.php");
        exit();
    }
    catch (Exception $e) {
        $conn->rollback();
        $_SESSION["alert"]["error"] = $e->getMessage();
        header("Location: ../pages/edit-profile.php");
        exit();
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
} // editUserAccount()

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
//    $setParts[] = "`updated_at` = NOW()";
//    print_r($setParts);


    return implode(", ", $setParts);
} // getSetClause()

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
        $query = <<<SQL
        SELECT
            p.post_id, 
            p.title, 
            p.content,
            p.created_at,
            p.updated_at, 
            ca.category_name, 
            COUNT(c.comment_id) AS comment_count, 
            pa.file_data, 
            pa.file_type
        FROM posts p 
        LEFT JOIN comments c ON p.post_id = c.post_id 
        JOIN categories ca ON p.category_id = ca.category_id 
        LEFT JOIN posts_attachments pa ON p.attachment_id = pa.attachment_id 
        WHERE p.user_id = ? 
        GROUP BY 
            p.post_id, 
            p.title,
            p.content,
            p.updated_at,
            ca.category_name
        ORDER BY
            p.updated_at DESC,
            comment_count DESC;
        SQL;
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
} // getUserPosts()

function editPost(array $post) : void {
    $conn = null;
    session_start();
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );

        // Jesli nie ma id uzytkownika - blad
        if (!isset($post["post-id"])) {
            throw new Exception("Brak identyfikatora posta");
        }
        $postId = (int) $post["post-id"];
        unset($post["post-id"]); // Nie aktualizujemy ID posta

        // Konwersja BBCode na HTML tresci posta
        if (isset($post["content"])) {
            include_once "../includes/bbcode-functions.php";
            $post["content"] = convertBBCodeToHTML($post["content"]);
        }

        // Przygotowanie zapytania UPDATE
        $setParts = [];
        $setClause = getSetClause($post, $conn, $setParts);

        $conn->begin_transaction();

        // Obsługa załącznika
        if (!empty($_FILES["attachment"]["tmp_name"]) && $_FILES["attachment"]["error"] == UPLOAD_ERR_OK) {
            // Sprawdzanie rozmiaru pliku (max 5 MB)
            $maxFileSize = 5 * 1024 * 1024; // 5 MB
            if ($_FILES["attachment"]["size"] > $maxFileSize) {
                $_SESSION["alert"]["error"] = "Plik jest za duży. Maksymalny rozmiar to 5 MB.";
                header("Location: ../pages/edit-post.php?postId=" . $postId);
                exit();
            }

            // Sprawdzanie formatu pliku
            $allowedExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "svg"];
            $fileExtension = strtolower(pathinfo($_FILES["attachment"]["name"], PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                $_SESSION["alert"]["error"] = "Nieobsługiwany format pliku. Dozwolone formaty to: JPG, PNG, GIF, BMP, SVG.";
                header("Location: ../pages/edit-post.php?postId=" . $postId);
                exit();
            }

            // Odczytanie zawartosci pliku
            $attachmentId = $post["attachment-id"] ?? null;
            $fileName = $_FILES["attachment"]["name"];
            $fileType = $_FILES["attachment"]["type"];
            $fileSize = $_FILES["attachment"]["size"];
            $fileData = file_get_contents($_FILES["attachment"]["tmp_name"]);

            // Jesli attachmentId jest null, dodajemy nowy załącznik
            if ($attachmentId == null) {
                $insertAttachmentQuery = <<<SQL
                INSERT INTO posts_attachments 
                    (file_name, file_type, file_size, file_data) 
                VALUES 
                    (?, ?, ?, ?)
                SQL;
                $stmt = $conn->prepare($insertAttachmentQuery);
                $stmt->bind_param("ssis", $fileName, $fileType, $fileSize, $fileData);
                $stmt->execute();

                // Pobieramy attachmentId z ostatnio dodanego załącznika
                $attachmentId = $conn->insert_id;

                // Dodajemy wpis do klauzuli UPDATE posts
                $setClause .= ", `attachment_id` = $attachmentId";
            }
            else {
                // Aktualizacja istniejacego zalacznika
                $updateAttachmentQuery = <<<SQL
                UPDATE 
                    posts_attachments 
                SET 
                    file_name = ?, 
                    file_type = ?, 
                    file_size = ?, 
                    file_data = ? 
                WHERE attachment_id = ?
                SQL;
                $stmt = $conn->prepare($updateAttachmentQuery);
                $stmt->bind_param("ssisi", $fileName, $fileType, $fileSize, $fileData, $attachmentId);
                $stmt->execute();
            }
        }
        $query = <<<SQL
            UPDATE
                posts 
            SET $setClause 
            WHERE post_id = $postId
        SQL;

        // Wykonujemy zapytanie
        $conn->query($query);
        $conn->commit();

        $_SESSION["editPostAlert"] = true;
        header("Location: ../pages/management-user-posts.php");
        exit;
    }
    catch (mysqli_sql_exception $e) {
        $conn->rollback();
        $_SESSION["alert"]["error"] = "Problem połączenia z bazą: " . $e->getMessage();
        echo $e->getMessage();
        header("Location: ../pages/management-user-posts.php");
        exit;
    }
    catch (Exception $e) {
        $conn->rollback();
        $_SESSION["alert"]["error"] = $e->getMessage();
        echo "exc ";
        echo $e->getMessage();
        header("Location: ../pages/management-user-posts.php");
        exit;
    }
} // editPost()

function getUsers_Admin() : array {
    $users = [];
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
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
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $users = $result->fetch_all(MYSQLI_ASSOC);
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
        return $users;
    }
} // getUsers_Admin()

function getPosts_Admin(int $category) : array {
    $posts = [];
    try {
//        if (empty($category)) {
//            $query
//        }
//        $categoryId = getCategoryId($category);
//        if ($categoryId == -1) {
//            throw new Exception("Nie znana kategoria");
//        }

        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = <<<SQL
        SELECT 
            p.post_id, 
            p.title, 
            p.content,
            p.created_at, 
            p.updated_at, 
            u.username, 
            u.email,
            COUNT(c.post_id) AS 'comments_count',
            pa.file_data, 
            pa.file_type 
        FROM posts p 
        JOIN users u ON p.user_id = u.user_id 
        LEFT JOIN posts_attachments pa ON p.attachment_id = pa.attachment_id
        LEFT JOIN comments c ON p.post_id = c.post_id
        WHERE p.category_id = ? 
        GROUP BY 
            p.post_id, 
            p.title, 
            p.content,
            p.created_at, 
            p.updated_at, 
            u.username, 
            u.email,
            pa.file_data, 
            pa.file_type
        ORDER BY p.created_at DESC
        SQL;
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
} // getPosts_Admin()

function getCategories() : array {
    $categories = [];
    try {
        $conn = new mysqli(
            MySQLConfig::SERVER,
            MySQLConfig::USER,
            MySQLConfig::PASSWORD,
            MySQLConfig::DATABASE
        );
        $query = <<<SQL
        SELECT
            category_id,
            category_name
        FROM categories
        ORDER BY category_name;
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result) {
            $categories = $result->fetch_all(MYSQLI_ASSOC);
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
        return $categories;
    }
} // getCategories()