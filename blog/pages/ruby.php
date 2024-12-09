<?php
session_start();
require_once "../includes/render-posts.php";
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

$language = "ruby";
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
    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.php"; ?>

        <section id="main-section">
            <h1>Ruby</h1>
            <p>
                Dynamiczny, interpretowany język programowania, stworzony z myślą o prostocie i produktywności programistów. Dzięki eleganckiej i czytelnej składni, Ruby ułatwia szybkie tworzenie oprogramowania. Jego najpopularniejszym zastosowaniem jest rozwój aplikacji webowych z wykorzystaniem frameworka Ruby on Rails, który upraszcza budowanie backendu poprzez podejście „konwencji nad konfiguracją”. Ruby on Rails umożliwia szybki rozwój aplikacji o wysokiej funkcjonalności i jest popularny w startupach i projektach webowych.
            </p>
            <img src="../images/ruby_logo.png" alt="Ruby logo" class="language-image">
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
</body>

</html>