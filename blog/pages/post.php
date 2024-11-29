<?php
session_start();
require_once "../includes/render-posts.php";

if (isset($_GET["postId"]) && is_numeric($_GET["postId"])) {
    $postId = (int)$_GET["postId"];  // Pobranie postId z URL

    include "../db/mysql-operation.php";
    $post = getOnePost($postId);
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

    <!-- Styles   -->
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.php"; ?>

    <section id="main-section">
        <h1><?php echo $post["title"]; ?></h1>
        <p> <?php echo $post["content"]; ?> </p>

        <article id="comments-section">
            <h3>Komentarze</h3>
            <div class="comments-container">
                <?php
                // renderAllPostComments(array_slice($comments, $offset, $commentsPerPage, true));
                // preserve_keys = true - zachowaj oryginalne klucze tablicy
                renderAllPostComments($comments);

                ?>
            </div>
        </article>
        <?php include "../includes/add-comment-form.php"; ?>

<!--        --><?php //renderPagination($currentPage, $totalPages, $language); ?>
    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.php"; ?>
</body>

</html>