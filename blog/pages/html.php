<?php
session_start();
require_once "../includes/render-posts.php";
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

$language = "html";
include "../db/mysql-operation.php";
$posts = getPosts($language);
$totalPosts = count($posts);
$postsPerPage = 3;

$paginationData = getPaginationData($currentPage, $totalPosts, $postsPerPage);
$currentPage = $paginationData["currentPage"];
$totalPages = $paginationData["totalPages"];
$offset = $paginationData["offset"];

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
<!--<div id="wrapper">  ??? -->

    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.php"; ?>

        <section id="main-section">
            <h1>HTML</h1>
            <p>
                HyperText Markup Language to podstawowy język służący do tworzenia stron internetowych. Jego głównym zadaniem jest strukturyzowanie treści, takich jak teksty, obrazy czy linki, oraz ich prawidłowe wyświetlanie w przeglądarce. HTML jest fundamentem każdej strony WWW i współpracuje z innymi technologiami, takimi jak CSS i JavaScript, by stworzyć pełnowartościową, interaktywną witrynę. Dzięki swojej prostocie jest idealny dla początkujących programistów.
            </p>
            <img src="../images/html_logo.png" alt="HTML logo" class="language-image">
            <?php if (isset($_SESSION["loggedUser"])): ?>
                <a href="add-post.php?category=<?php echo $language;?>" class="post-comments-link add-post-link">Dodaj post</a>
            <?php endif ?>

            <article id="comments-section">
                <h3>Posty</h3>
                <div class="comment-container">

                <?php
                renderPosts(array_slice($posts, $offset, $postsPerPage, true));
                // preserve_keys = true - zachowaj oryginalne klucze tablicy
                ?>
                </div>
            </article>

            <?php renderPagination($currentPage, $totalPages, $language); ?>

        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.php"; ?>
<!--</div>-->
</body>

</html>