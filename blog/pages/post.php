<?php
session_start();
require_once "../includes/posts-functions.php";

if (isset($_GET["postId"]) && is_numeric($_GET["postId"])) {
    $postId = (int)$_GET["postId"];  // Pobranie postId z URL

    include "../db/mysql-operation.php";
    $post = getOnePost($postId);
    if (count($post) == 0 ) {
        http_response_code(404); // Not Found - nie znaleziono zasobu
        require "../errors/404.html";
        exit;
    }
    $comments = getCommentsToPost($postId);
}
else {
    http_response_code(400); // Bad request - bledna skladnia
    require "../errors/400.html";
    exit;
}

?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
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
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.php"; ?>

    <section id="main-section">
        <?php
            echo "<h1>" . $post["title"] . "</h1>";
            echo "<p class='post-author'>Autor: " . $post["username"]. ", " . $post["email"] .
            "<span class='post-date'>Utworzono: " . date("d-m-Y H:i", strtotime($post["created_at"])) .
             "<span class='post-updated'>| Ostatnia aktualizacja: " . date('d-m-Y H:i', strtotime($post["updated_at"])) . "</span></span></p>";
            echo "<p>" . $post["content"] . "</p>";

            if (!empty($post["file_data"]) && str_starts_with($post["file_type"], "image")) {
            // Wyswietlanie zalaczonego zdjecia, jeśsi istnieje
            $base64Image = base64_encode($post["file_data"]);
            echo "<h5>Załączone zdjęcie:</h5>";
            echo "<img src='data:" . htmlspecialchars($post["file_type"]) . ";base64," . $base64Image . "' alt='Załączone zdjęcie' class='post-attachment'>";
        }
        ?>

        <?php if (isset($_SESSION["addCommentAlert"]) && $_SESSION["addCommentAlert"]["result"]): ?>
            <div class="alert alert-success">
                <strong>Sukces!</strong> Dodano nowy komentarz
            </div>
            <?php
            unset($_SESSION["addCommentAlert"]);
        endif ?>

        <?php if (isset($_SESSION["addCommentAlert"]) && !$_SESSION["addCommentAlert"]["result"]): ?>
            <div class="alert alert-danger">
                <strong>Błąd!</strong> <?php echo $_SESSION["addCommentAlert"]["error"] ?>
            </div>
            <?php
            unset($_SESSION["addCommentAlert"]);
        endif ?>

        <article id="comments-section">
            <h3>Komentarze</h3>
            <div class="comments-container">
                <?php renderAllPostComments($comments); ?>
            </div>
        </article>
        <?php include "../includes/add-comment-form.php"; ?>

    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.php"; ?>
</body>

</html>