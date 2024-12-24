<?php
session_start();
require_once "../errors/error-codes.php";
require_once "../includes/posts-functions.php";
require_once "../db/mysql-operation.php";

// Sprawdzenie czy postId jest dostepne i poprawne
if (!isset($_GET["postId"]) || !is_numeric($_GET["postId"])) {
    http_response_code(HttpStatus::BAD_REQUEST);
    require "../errors/400.html";
    exit();
}

$postId = (int)$_GET["postId"];  // Pobranie postId z URL
$post = getOnePost($postId);

if (empty($post)) {
    http_response_code(HttpStatus::NOT_FOUND);
    require "../errors/404.html";
    exit();
}

$comments = getCommentsToPost($postId);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog | <?= htmlspecialchars($post["title"], ENT_QUOTES | ENT_HTML5); ?></title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../images/favicons/favicon-16x16.png">
    <link rel="manifest" href="../images/favicons/site.webmanifest">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

    <!-- Styles   -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style-table-stats.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/admin-posts.js" type="module"></script>
</head>
<body>
    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.html"; ?>

        <section id="main-section">
            <?php
                echo "<img class='language-image' src='../images/" . strtolower($post["category_name"]) . "_logo.png' alt='" . $post["category_name"] . "' title='" . $post["category_name"] . "'>";
                echo "<h2>" . $post["title"];
                if (isset($_SESSION["loggedUser"]) && $_SESSION["loggedUser"]["role"] == "Admin") {
                    echo "<button class='post-link delete-button' data-post-id='" . $post["post_id"] . "' data-category-name='" . $post["category_name"] . "' title='Usuń post'>";
                    echo "<img src='../images/trash-fill.svg' alt='Usuń post'></button>";
                }
                echo "</h2>";

                echo "<p class='post-author'>Autor: " . $post["username"]. ", " . $post["email"] .
                "<span class='post-date'>Utworzono: " . date("d-m-Y H:i", strtotime($post["created_at"])) .
                 "<span class='post-updated'>| Ostatnia aktualizacja: " . date("d-m-Y H:i", strtotime($post["updated_at"])) . "</span></span></p>";
                echo "<p>" . $post["content"] . "</p>";

                if (!empty($post["file_data"]) && str_starts_with($post["file_type"], "image")) {
                // Wyswietlanie zalaczonego zdjecia, jesli istnieje
                $base64Image = base64_encode($post["file_data"]);
                echo "<h5>Załączone zdjęcie:</h5>";
                echo "<img src='data:" . htmlspecialchars($post["file_type"]) . ";base64," . $base64Image . "' alt='Załączone zdjęcie' class='post-attachment'>";
            }
            ?>

            <?php if (isset($_SESSION["addCommentAlert"]) && $_SESSION["addCommentAlert"]["result"]): ?>
                <div class="alert alert-success">
                    <strong>Sukces!</strong> Dodano nowy komentarz
                </div>
                <?php unset($_SESSION["addCommentAlert"]); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION["addCommentAlert"]) && !$_SESSION["addCommentAlert"]["result"]): ?>
                <div class="alert alert-danger">
                    <strong>Błąd!</strong> <?php echo $_SESSION["addCommentAlert"]["error"] ?>
                </div>
                <?php unset($_SESSION["addCommentAlert"]); ?>
            <?php endif; ?>

            <article id="comments-section">
                <h3>Komentarze</h3>
                <div class="comments-container">
                    <?php renderAllPostComments($comments); ?>
                </div>
            </article>
            <?php require_once "../includes/add-comment-form.php"; ?>

            <!-- Modal usuwania posta/komentarza przez admina -->
            <div id="delete-post-modal" class="modal delete-modal">
                <div class="modal-content">
                    <p>Czy na pewno chcesz usunąć ten <span></span>?</p>
                    <div class="modal-buttons">
                        <button id="cancel-button" class="modal-button cancel-button">Anuluj</button>
                        <button id="confirm-button" class="modal-button confirm-button">Potwierdź</button>
                    </div>
                </div>
            </div>

        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.html"; ?>

</body>
</html>