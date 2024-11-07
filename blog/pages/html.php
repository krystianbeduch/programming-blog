<?php
session_start();
require_once "../includes/pagination.php";
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

$comments = [
    "Przykładowy komentarz 1",
    "Przykładowy komentarz 2",
    "Przykładowy komentarz 3",
    "Przykładowy komentarz 4",
    "Przykładowy komentarz 5",
    "Przykładowy komentarz 6",
    "Przykładowy komentarz 7",
    "Przykładowy komentarz 8",
    "Przykładowy komentarz 9",
    "Przykładowy komentarz 10",
    "Przykładowy komentarz 11",
    "Przykładowy komentarz 12",
];
$totalComments = count($comments);
$commentsPerPage = 5;

$paginationData = getPaginationData($currentPage, $totalComments, $commentsPerPage);
$currentPage = $paginationData["currentPage"];
$totalPages = $paginationData["totalPages"];
$offset = $paginationData["offset"];

$language = "html";
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
<div id="wrapper"> <!-- ??? -->

    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.php"; ?>

        <section id="main-section">
            <h1>HTML</h1>
            <p>
                HyperText Markup Language to podstawowy język służący do tworzenia stron internetowych. Jego głównym zadaniem jest strukturyzowanie treści, takich jak teksty, obrazy czy linki, oraz ich prawidłowe wyświetlanie w przeglądarce. HTML jest fundamentem każdej strony WWW i współpracuje z innymi technologiami, takimi jak CSS i JavaScript, by stworzyć pełnowartościową, interaktywną witrynę. Dzięki swojej prostocie jest idealny dla początkujących programistów.
            </p>
            <img src="../images/html_logo.png" alt="HTML logo" class="language-image">

            <article id="comments-section">
                <h3>Posty</h3>
                <div class="comment-container">

                <?php renderPosts(array_slice($comments, $offset, $commentsPerPage, true));
                    // preserve_keys - zachowaj oryginalne klucze tablicy
                ?>
                </div>
            </article>
            <?php include "../includes/form.php"; ?>

            <?php renderPagination($currentPage, $totalPages, $language); ?>

        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.php"; ?>
</div>
</body>

</html>