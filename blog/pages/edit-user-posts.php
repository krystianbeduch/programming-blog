<?php
session_start();
if (!isset($_SESSION["loggedUser"])) {
    http_response_code(401); // Unauthorized - nieuprawniony dostep
    require "../errors/401.html";
    exit;
}

//session_destroy();
require_once "../includes/render-posts.php";
$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

//$language = "php";
include "../db/mysql-operation.php";
$userPosts= getUserPosts($_SESSION["loggedUser"]["id"]);
$totalPosts = count($userPosts);
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
    <link rel="stylesheet" href="../css/style-posts.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/edit-user-form.js"></script>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.php"; ?>

    <section id="main-section">
        <h1>PHP</h1>
        <p>
            Język skryptowy, który jest szeroko stosowany do tworzenia dynamicznych stron internetowych i aplikacji serwerowych. Jest szczególnie przydatny przy tworzeniu systemów zarządzania treścią (CMS), takich jak WordPress, oraz w integracji z bazami danych, np. MySQL. PHP jest łatwy do nauki i oferuje szerokie możliwości, co czyni go idealnym narzędziem do tworzenia serwisów webowych o różnym stopniu zaawansowania.
        </p>
        <img src="../images/php_logo.png" alt="PHP logo" class="language-image">

        <?php if (isset($_SESSION["addPostAlert"]) && $_SESSION["addPostAlert"]["result"]): ?>
            <div class="alert alert-success">
                <strong>Sukces!</strong> Dodano nowy post
            </div>
            <?php
            unset($_SESSION["addPostAlert"]);
        endif ?>

        <?php if (isset($_SESSION["addPostAlert"]) && !$_SESSION["addPostAlert"]["result"]): ?>
            <div class="alert alert-danger">
                <strong>Błąd!</strong> <?php echo $_SESSION["addPostAlert"]["error"] ?>
            </div>
            <?php
            unset($_SESSION["addPostAlert"]);
        endif ?>



<!--        --><?php //if (isset($_SESSION["loggedUser"])): ?>
<!--            <a href="add-post.php?category=--><?php //echo $language;?><!--" class="post-comments-link add-post-link">Dodaj post</a>-->
<!--        --><?php //endif ?>

        <article id="posts-section">
            <h3>Posty użytkownika
                <em>
                    <?php echo $_SESSION["loggedUser"]["username"]?>
                </em>
            </h3>
            <div class="posts-container">
                <?php
//                renderUserPosts($_SESSION["loggedUser"]["id"]);
                renderUserPosts(array_slice($userPosts, $offset, $postsPerPage, true));
                ?>
            </div>
        </article>

        <?php renderPaginationUserPosts($currentPage, $totalPages); ?>
    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.php"; ?>
</body>

</html>