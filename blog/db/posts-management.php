<?php
require_once "db-connect.php";
require_once "mysql-operation.php";

function getCategoryDescription(string $category): string {
    $conn = null;
    $stmt = null;
    $description = "";
    try {
        $categoryId = getCategoryId($category);
        if ($categoryId == -1) {
            throw new Exception("Nieznana kategoria");
        }
        $conn = createMySQLiConnection();
        $query = <<<SQL
        SELECT 
            description 
        FROM categories 
        WHERE category_id = ?;
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $categoryId);
        $stmt->execute();
        $description = $stmt->get_result()->fetch_object()->description ?? "";
    }
    catch (mysqli_sql_exception|Exception $e) {
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
    return $description;
} // getCategoryDescription()

function getCategoryId(string $category) : int {
    $conn = null;
    $stmt = null;
    try {
        $conn = createMySQLiConnection();
        $query = <<<SQL
        SELECT
            category_id
        FROM categories
        WHERE category_name = ?;
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $categoryId = $stmt->get_result()->fetch_object()->category_id ?? -1;
    }
    catch (mysqli_sql_exception|Exception $e) {
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
    return $categoryId;
} // getCategoryId()

function getPosts(?string $category = null) : array {
    $conn = null;
    $stmt = null;
    try {
        $conn = createMySQLiConnection();
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

        $stmt = $conn->prepare($query);
        if ($category) {
            $stmt->bind_param("i", $categoryId);
        }
        $stmt->execute();
        $posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    catch (mysqli_sql_exception|Exception $e) {
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
    return $posts;
} // getPosts()

function getOnePost(int $postId): array {
    try {
        $conn = createMySQLiConnection();
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
        // if fetch_assoc then fetch_assoc else []
        $post = $stmt->get_result()->fetch_assoc() ?: [];
    }
    catch (mysqli_sql_exception|Exception $e) {
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
    return $post;
}// getOnePost()

function getOnePostToEdit(int $userId, int $postId): array {
    $headerLocation = "../pages/management-user-posts.php";
    try {
        $conn = createMySQLiConnection();
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
        $post = $stmt->get_result()->fetch_assoc() ?: [];
    }
    catch (mysqli_sql_exception|Exception $e) {
        handleDatabaseError($e, $headerLocation);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
    return $post;
} // getOnePostToEdit()

function getCommentsToPost(int $postId) : array {
    $conn = null;
    $stmt = null;
    try {
        $conn = createMySQLiConnection();
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
        $comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?? [];
    }
    catch (mysqli_sql_exception|Exception $e) {
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
    return $comments;
} // getCommentsToPost()

function addCommentToPost(array $commentData) : void {
//    session_start();
    $postId = $commentData["post-id"];
    $headerLocation = "../pages/post.php?postId=" . $postId;
    $conn = null;
    $stmt = null;

    try {
        $conn = createMySQLiConnection();

        // Rozpoczecie transakcji
        $conn->begin_transaction();

        //////////////////// POTRZEBNE ZAPYTANIE KTORE DODAJE UZYTKOWNIKA JAKO JEGO USER_ID
        // Przygotowanie i wykonanie zapytania
        $query = <<<SQL
        INSERT INTO comments
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

        $_SESSION["alert"]["success"] = "Dodano nowy post";

        // Zatwierdzenie transkacji
        $conn->commit();

        header("Location: ../pages/post.php?postId=" . $postId);
    }
    catch (mysqli_sql_exception|Exception $e) {
        $conn->rollback();
        handleDatabaseError($e, $headerLocation);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
} // addCommentToPost()

function checkCategory(string $language) : bool {
    $conn = null;
    $stmt = null;
    $result = false;
    try {
        $conn = createMySQLiConnection();
        $query = <<<SQL
        SELECT 
            1 
        FROM categories 
        WHERE category_name = ?;
        SQL;
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $language);
        $stmt->execute();
        $result = $stmt->get_result()->num_rows > 0;
    }
    catch (mysqli_sql_exception|Exception $e) {
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
    return $result;
} // checkCategory()

function addPost(array $postData) : void {
    $conn = null;
    $stmt = null;
    $headerLocation = "../pages/" . $postData["category"] . ".php";
    try {
        $attachmentId = isset($_SESSION["uploaded_file"]) ? addAttachmentToPost($_SESSION["uploaded_file"]) : null;
        unset($_SESSION["uploaded_file"]);

        $conn = createMySQLiConnection();
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

        $_SESSION["alert"]["success"] = "Dodano nowy post";
        $conn->commit();
        header("Location: $headerLocation");
    }
    catch (mysqli_sql_exception|Exception $e) {
        $conn->rollback();
        handleDatabaseError($e, $headerLocation);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
} // addPost()

function addAttachmentToPost($attachmentData) : int {
    $fileName = $attachmentData["name"];
    $fileType = $attachmentData["type"];
    $fileSize = $attachmentData["size"];
    $fileContent = base64_decode($attachmentData["content"]);
    $conn = null;
    $stmt = null;
    try {
        $conn = createMySQLiConnection();
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
    catch (mysqli_sql_exception|Exception $e) {
        $conn->rollback();
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
    return $insertedId;
} // addAtachmentToPost()

function getUserPosts(int $userId) : array {
    $comments = [];
    $conn = null;
    $stmt = null;
    try {
        $conn = createMySQLiConnection();
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
        $comments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?? [];
    }
    catch (mysqli_sql_exception|Exception $e) {
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
    return $comments;
} // getUserPosts()

function editPost(array $post) : void {
    $conn = null;
    $stmt = null;
    $headerLocation = "../pages/management-user-posts.php";
    try {
        $conn = createMySQLiConnection();

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

        // Wykonaj aktualizacje o ile zmiana zalacznika nie byla jedyna
        if ($setClause) {
            $query = <<<SQL
                UPDATE
                    posts 
                SET $setClause 
                WHERE post_id = ?             
                SQL;
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $postId);
            $stmt->execute();
        }
        $conn->commit();

        $_SESSION["alert"]["successStrong"] = "Zapisano zmiany!";
        $_SESSION["alert"]["success"] = "Post zaktualizowany";
        header("Location: $headerLocation");
    }
    catch (mysqli_sql_exception|Exception $e) {
        $conn->rollback();
        handleDatabaseError($e);
    }
    finally {
        $stmt?->close();
        $conn?->close();
    }
} // editPost()