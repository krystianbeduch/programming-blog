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
    "Przykładowy komentarz 7"
];
$totalComments = count($comments);
$commentsPerPage = 5;

$paginationData = getPaginationData($currentPage, $totalComments, $commentsPerPage);
$currentPage = $paginationData["currentPage"];
$totalPages = $paginationData["totalPages"];
$offset = $paginationData["offset"];

$language = "csharp";
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
            <h1>C#</h1>
            <p>
                C-Sharp to nowoczesny, obiektowy język programowania opracowany przez Microsoft, który jest powszechnie używany do budowania aplikacji na platformę .NET. Dzięki swojej elastyczności, C# znajduje zastosowanie zarówno w tworzeniu aplikacji desktopowych, jak i gier komputerowych, zwłaszcza z wykorzystaniem silnika Unity. W sferze backendu, C# jest używany do tworzenia zaawansowanych aplikacji serwerowych, zwłaszcza w środowiskach opartych na Windows, z wykorzystaniem platformy ASP.NET, co umożliwia budowanie wydajnych i skalowalnych serwisów.
            </p>
            <img src="../images/csharp_logo.png" alt="C# logo">

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