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
    "Przykładowy komentarz 7",
    "Przykładowy komentarz 8",
    "Przykładowy komentarz 9",
    "Przykładowy komentarz 10",
    "Przykładowy komentarz 11",
    "Przykładowy komentarz 12",
    "Przykładowy komentarz 13",
    "Przykładowy komentarz 14",
    "Przykładowy komentarz 15",
    "Przykładowy komentarz 16",
    "Przykładowy komentarz 17",
    "Przykładowy komentarz 18",
    "Przykładowy komentarz 19",
    "Przykładowy komentarz 20",
    "Przykładowy komentarz 21",
];
$totalComments = count($comments);
$commentsPerPage = 5;

$paginationData = getPaginationData($currentPage, $totalComments, $commentsPerPage);
$currentPage = $paginationData["currentPage"];
$totalPages = $paginationData["totalPages"];
$offset = $paginationData["offset"];

$language = "java";
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
            <h1>Java</h1>
            <p>
                Wszechstronny, obiektowy język programowania, który jest używany do budowy rozbudowanych aplikacji desktopowych, webowych oraz mobilnych. Jego główną zaletą jest przenośność – kod napisany w Javie może działać na różnych platformach dzięki mechanizmowi JVM (Java Virtual Machine). Java znajduje szerokie zastosowanie w systemach backendowych, gdzie wraz z popularnymi frameworkami, takimi jak Spring czy Hibernate, umożliwia tworzenie skalowalnych i wydajnych aplikacji serwerowych. Dzięki temu jest jednym z najczęściej wybieranych języków w dużych korporacyjnych systemach i rozwiązaniach o wysokiej wydajności.
            </p>
            <img src="../images/java_logo.png" alt="Java logo">

            <br><br><br><br><br>
            <?php
            $article_header = array("wpis1", "wpis2", "wpis3", "wpis4", "wpis5");
            $article_content = array("zawartosc1", "zawartosc2", "zawartosc3", "zawartosc4", "zawartosc5");
            $article_footer = array("stopka1", "stopka2", "stopka3", "stopka4", "stopka5");

            for ($i = 0; $i < count($article_header); $i++) {
                echo "<article class='test-article'>";
                echo "<header>";
                echo $article_header[$i];
                echo "</header>";
                echo "<p>";
                echo $article_content[$i];
                echo "</p>";
                echo "<footer>";
                echo $article_footer[$i];
                echo "</footer>";
                echo "</article>";
            }
            ?>

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