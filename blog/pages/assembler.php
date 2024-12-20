<?php
session_start();
require_once "../includes/posts-functions.php";
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

$language = "assembler";
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

    <!-- Styles   -->
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>
<?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.php"; ?>

        <section id="main-section">
            <h1>Asembler</h1>
            <p>
                Niskopoziomowy język programowania, który pozwala na bezpośrednie komunikowanie się z procesorem komputera. Jest używany tam, gdzie wymagana jest pełna kontrola nad zasobami sprzętowymi, np. w systemach operacyjnych, sterownikach i systemach wbudowanych. Asembler, choć trudniejszy w nauce i pisaniu niż wyżej poziomowe języki, umożliwia maksymalną optymalizację kodu, co jest kluczowe w systemach o ograniczonych zasobach. Jego znajomość jest niezbędna dla programistów tworzących krytyczne oprogramowanie, które musi działać blisko sprzętu.
            </p>
            <img src="../images/assembler_logo.png" alt="Assembler logo" class="language-image">

            <?php include_once "../includes/post-alerts.php"; ?>

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