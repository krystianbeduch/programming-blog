<?php
require_once "../includes/page-setup.php";
require_once "../errors/error-codes.php";

if (!isset($_SESSION["loggedUser"])) {
    http_response_code(HttpStatus::UNAUTHORIZED);
    require "../errors/401.html";
    exit();
}
$pageData = new PageSetup($_SESSION["loggedUser"]["id"]);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog | Zarządzanie postami</title>
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
    <link rel="stylesheet" href="../css/style-table-stats.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/user-posts-management.js" type="module"></script>
</head>
<body>
    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.html"; ?>

        <section id="main-section">
            <h2>Statystyki postów</h2>
            <button class="toggle-stats-table form-button">Pokaż tabele ze statystykami</button>

            <?php renderUserPostsStats($pageData->posts); ?>

            <?php if (isset($_SESSION["addPostAlert"]) && $_SESSION["addPostAlert"]["result"]): ?>
                <div class="alert alert-success">
                    <strong>Sukces!</strong> Dodano nowy post
                </div>
                <?php
                    unset($_SESSION["addPostAlert"]);
                endif; ?>

            <?php if (isset($_SESSION["addPostAlert"]) && !$_SESSION["addPostAlert"]["result"]): ?>
                <div class="alert alert-danger">
                    <strong>Błąd!</strong> <?= $_SESSION["addPostAlert"]["error"]; ?>
                </div>
                <?php
                    unset($_SESSION["addPostAlert"]);
                endif; ?>

            <article id="posts-section">
                <h3>Posty użytkownika
                    <em>
                        <?= $_SESSION["loggedUser"]["username"]; ?>
                    </em>
                </h3>
                <div class="posts-container">
                    <?php renderUserPosts(array_slice($pageData->posts, $pageData->getOffset(), $pageData->postsPerPage, true)); ?>
                </div>
            </article>

            <?php if(count($pageData->posts) > 0)
                renderPaginationUserPosts($pageData->getCurrentPage(), $pageData->getTotalPages()); ?>
        </section>

        <div id="delete-post-modal" class="modal delete-modal">
            <div class="modal-content">
                <p>Czy na pewno chcesz usunąć ten post?</p>
                <div class="modal-buttons">
                    <button id="cancel-button" class="modal-button cancel-button">Anuluj</button>
                    <button id="confirm-button" class="modal-button confirm-button">Potwierdź</button>
                </div>
            </div>
        </div>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.html"; ?>

</body>
</html>