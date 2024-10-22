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

    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.php"; ?>

        <section id="main-section">
            <h1>CSS</h1>
            <p>
                Cascading Style Sheets to język służący do opisywania wyglądu i stylu dokumentów HTML. Pozwala na zdefiniowanie układu elementów, kolorów, czcionek, animacji oraz wielu innych aspektów wizualnych strony internetowej. CSS oddziela treść od prezentacji, co ułatwia zarządzanie stylem witryny oraz jej estetykę na różnych urządzeniach. W połączeniu z HTML i JavaScript tworzy podstawowy zestaw technologii do tworzenia nowoczesnych, responsywnych stron internetowych.
            </p>
            <img src="../images/css_logo.png" alt="CSS logo">
        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.php"; ?>
</body>

</html>