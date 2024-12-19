<?php
session_start();
if (!isset($_SESSION["loggedUser"])) {
    http_response_code(401); // Unauthorized - nieuprawniony dostep
    require "../errors/401.html";
    exit;
}

//session_destroy();
require_once "../includes/posts-functions.php";
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
    <script src="../js/user-posts-management.js"></script>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.php"; ?>

    <section id="main-section">
        <h1>Statystyki postów</h1>
        <button class="toggle-stats-table form-button">Pokaż tabele ze statystykami</button>

            <?php renderUserPostsStats($userPosts) ?>

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
                    renderUserPosts(array_slice($userPosts, $offset, $postsPerPage, true));
                ?>
            </div>
        </article>

        <?php if(count($userPosts) > 0) renderPaginationUserPosts($currentPage, $totalPages); ?>
    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<div id="delete-post-modal" class="modal">
    <div class="modal-content">
        <p>Czy na pewno chcesz usunąć ten post?</p>
        <div class="modal-buttons">
            <button id="cancel-button" class="modal-button cancel-button">Anuluj</button>
            <button id="confirm-button" class="modal-button confirm-button">Potwierdź</button>
        </div>
    </div>
</div>

<?php require_once "../includes/footer.php"; ?>



</body>

</html>