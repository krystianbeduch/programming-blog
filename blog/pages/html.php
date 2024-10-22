<?php
session_start();
require_once "../includes/pagination.php";
//require_once "../includes/pagination-config.php";
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalPages = 15;

$languages = [
    1 => ['name' => 'HTML', 'file' => 'html.php'],
    2 => ['name' => 'CSS', 'file' => 'css.php'],
    3 => ['name' => 'JavaScript', 'file' => 'javascript.php'],
    4 => ['name' => 'TypeScript', 'file' => 'typescript.php'],
    5 => ['name' => 'PHP', 'file' => 'php.php'],
    6 => ['name' => 'Java', 'file' => 'java.php'],
    7 => ['name' => 'Python', 'file' => 'python.php'],
    8 => ['name' => 'C#', 'file' => 'csharp.php'],
    9 => ['name' => 'Ruby', 'file' => 'ruby.php'],
    10 => ['name' => 'Assembler', 'file' => 'assembler.php'],
    11 => ['name' => 'C', 'file' => 'c.php'],
    12 => ['name' => 'C++', 'file' => 'cpp.php'],
    13 => ['name' => 'SQL', 'file' => 'sql.php'],
    14 => ['name' => 'Kotlin', 'file' => 'kotlin.php'],
    15 => ['name' => "Swift", 'file' => 'swift.php'],
];

$currentLanguage = $languages[$currentPage] ?? null;
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="../images/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="../images/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../images/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../images/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../images/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../images/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../images/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../images/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../images/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="../images/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../images/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../images/favicons/favicon-16x16.png">
    <link rel="manifest" href="../images/favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

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
            <h1><?php echo $currentLanguage['name']; ?></h1>
            <p>
                HyperText Markup Language to podstawowy język służący do tworzenia stron internetowych. Jego głównym zadaniem jest strukturyzowanie treści, takich jak teksty, obrazy czy linki, oraz ich prawidłowe wyświetlanie w przeglądarce. HTML jest fundamentem każdej strony WWW i współpracuje z innymi technologiami, takimi jak CSS i JavaScript, by stworzyć pełnowartościową, interaktywną witrynę. Dzięki swojej prostocie jest idealny dla początkujących programistów.
            </p>
            <img src="../images/html_logo.png" alt="HTML logo">

            <article id="comments-section">
                <h3>Posty</h3>
                <div class="comment-container">
                    <div class="comment">
                        <h4 class="comment-author">Autor 1</h4>
                        <p class="comment-author-email">Email 1</p>
                        <p class="comment-text">Przykładowy komentarz</p>
                    </div>
                    <div class="comment">
                        <h4 class="comment-author">Autor 2</h4>
                        <p class="comment-author-email">Email 2</p>
                        <p class="comment-text">Przykładowy komentarz 2</p>
                    </div>
                </div>
            </article>
            <?php include "../includes/form.php"; ?>

            <?php renderPagination($currentPage, $totalPages, $languages); ?>

            <?php require_once "../includes/pagination.php"; ?>

        </section>

        <?php require_once "../includes/aside.php"; ?>


    </main>

    <?php require_once "../includes/footer.php"; ?>
</body>

</html>