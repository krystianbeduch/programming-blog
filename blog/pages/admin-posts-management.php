<?php
session_start();
require_once "../errors/error-codes.php";

if (!isset($_SESSION["loggedUser"])) {
    http_response_code(HttpStatus::UNAUTHORIZED);
    require "../errors/401.html";
    exit();
}

require_once "../includes/admin-functions.php";

$category = $_GET["category"] ?? null;
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | Panel administracyjny</title>
    <link rel="stylesheet" href="../css/style-table-stats.css">
    <script src="../js/admin-posts.js" type="module"></script>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section">
        <h2>Panel Administracyjny - zarządzanie postami</h2>
        <?php renderFilter($category); ?>
        <table id="admin-posts-stats" class="table-stats">
            <colgroup>
                <col style="width: 2%;">
                <col style="width: 2%">
                <col style="width: auto">
                <col style="width: 25%;">
                <col style="width: 11%">
                <col style="width: 11%">
                <col style="width: 2%">
                <col style="width: 13%;">
                <col>
            </colgroup>
            <thead>
            <tr><th>ID</th><th>Kategoria</th><th>Tytuł</th><th>Autor</th><th>Data utworzenia</th><th>Data aktualizacji</th><th>Komentarze</th><th>Akcje</th></tr>
            </thead>
            <tbody>
            <?php renderPosts_Admin($category); ?>
            </tbody>
        </table>

        <!-- Kontener na podglad tresci posta -->
        <div id="preview-container" class="preview-container" style="display: none;">
            <h4>Treść posta</h4>
            <div id="preview-content" class="preview-content"></div>
            <button type="button" class="close close-preview-button">Zamknij podgląd</button>
        </div>

        <!-- Modal usuwania posta -->
        <div id="delete-post-modal" class="modal delete-modal">
            <div class="modal-content">
                <p>Czy na pewno chcesz usunąć ten post?</p>
                <div class="modal-buttons">
                    <button id="cancel-button" class="modal-button cancel-button">Anuluj</button>
                    <button id="confirm-button" class="modal-button confirm-button">Potwierdź</button>
                </div>
            </div>
        </div>

    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>