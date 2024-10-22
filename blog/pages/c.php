<?php
session_start();
require_once "../includes/pagination.php";
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$comments = [
    "Przykładowy komentarz 1",
    "Przykładowy komentarz 2",
    "Przykładowy komentarz 3",
    "Przykładowy komentarz 4",
    "Przykładowy komentarz 5",
    "Przykładowy komentarz 6",
    "Przykładowy komentarz 7"
];
$totalComments = count($comments);
$commentsPerPage = 5;

$paginationData = getPaginationData($currentPage, $totalComments, $commentsPerPage);
$currentPage = $paginationData["currentPage"];
$totalPages = $paginationData["totalPages"];
$offset = $paginationData["offset"];

$language = "c";
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

    <!--  Styles -->
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style-form.css">
    <link rel="stylesheet" href="../css/style-comments.css">
</head>
<body>
    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.php"; ?>

        <section id="main-section">
            <h1>C</h1>
            <p>
                Jeden z najstarszych i najbardziej wpływowych języków programowania, który stanowi fundament wielu nowoczesnych technologii. Dzięki swojej niskopoziomowej naturze, C jest szeroko używany do tworzenia systemów operacyjnych, sterowników, oprogramowania sprzętowego oraz innych aplikacji wymagających bezpośredniego dostępu do zasobów sprzętowych. Jego efektywność i kontrola nad pamięcią czynią go idealnym do programowania systemów wbudowanych i krytycznych aplikacji, gdzie wydajność jest kluczowa.
            </p>
            <img src="../images/c_logo.png" alt="C logo">

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
</body>

</html>