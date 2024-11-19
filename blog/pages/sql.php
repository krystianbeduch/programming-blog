<?php
session_start();
require_once "../includes/render-posts.php";
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
    "Przykładowy komentarz 11"
];
$totalComments = count($comments);
$commentsPerPage = 5;

$paginationData = getPaginationData($currentPage, $totalComments, $commentsPerPage);
$currentPage = $paginationData["currentPage"];
$totalPages = $paginationData["totalPages"];
$offset = $paginationData["offset"];

$postId = "sql";
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
            <h1>SQL</h1>
            <p>
                Structured Query Language to język służący do zarządzania i manipulacji danymi w relacyjnych bazach danych. Jest standardem do tworzenia, modyfikowania i pobierania danych z baz danych takich jak MySQL, PostgreSQL, Oracle czy Microsoft SQL Server. SQL jest szeroko stosowany w aplikacjach, które wymagają przechowywania i przetwarzania dużych ilości danych, zarówno w systemach frontendowych, jak i backendowych. Jego zastosowanie obejmuje również analizy danych, raportowanie oraz zarządzanie danymi w biznesowych aplikacjach i systemach ERP.
            </p>
            <img src="../images/sql_logo.png" alt="SQL logo" class="language-image">

            <article id="comments-section">
                <h3>Posty</h3>
                <div class="comment-container">

                    <?php renderPostComments(array_slice($comments, $offset, $commentsPerPage, true));
                    // preserve_keys - zachowaj oryginalne klucze tablicy
                    ?>
                </div>
            </article>
            <?php include "../includes/form.php"; ?>

            <?php renderPagination($currentPage, $totalPages, $postId); ?>

        </section>
        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.php"; ?>
</body>

</html>