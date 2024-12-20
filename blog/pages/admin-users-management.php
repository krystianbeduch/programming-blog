<?php
session_start();
if (!isset($_SESSION["loggedUser"]) && $_SESSION["loggedUser"]["role"] != "Admin") {
    http_response_code(401); // Unauthorized - nieuprawniony dostep
    require "../errors/401.html";
    exit;
}

require_once "../includes/admin-functions.php";

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

    <!-- Styles -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style-admin.css">
    <link rel="stylesheet" href="../css/style-table-stats.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!--    <script src="../js/edit-user-post-form.js" type="module"></script>-->
        <script src="../js/admin-users.js" type="module"></script>
<!--    <script src="../js/add-comment-bbcode.js"></script>-->
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.php"; ?>

    <section id="main-section">
        <h1>Panel Administracyjny - zarządzanie użytkownikami</h1>
        <table id="admin-users-tab" class="table-stats">
            <colgroup>
                <col style="width: 2%;">
                <col>
                <col>
                <col>
                <col style="width: 5%;">
                <col>
                <col>
                <col style="width: 5%;">
                <col>
                <col>
            </colgroup>
            <thead>
                <tr><th>ID</th><th>Użytkownik</th><th>Email</th><th>O mnie</th><th>Liczba postów</th><th>Data utworzenia</th><th>Ostatnia aktualizacja</th><th>Aktywność konta</th><th>Rola</th><th>Akcje</th></tr>
            </thead>
            <tbody>
                <?php renderUsers(); ?>
            </tbody>
        </table>

        <!-- Kontener na podglad -->
        <div id="preview-container" class="preview-container" style="display: none;">
            <h4>Podgląd "O mnie"</h4>
            <div id="preview-content" class="preview-content"></div>
            <button type="button" class="close close-preview-button">Zamknij podgląd</button>
        </div>

        <div id="delete-user-modal" class="modal delete-modal">
            <div class="modal-content">
                <p>Czy na pewno chcesz usunąć tego użytkownika?</p>
                <div class="modal-buttons">
                    <button id="cancel-button" class="modal-button cancel-button">Anuluj</button>
                    <button id="confirm-button" class="modal-button confirm-button">Potwierdź</button>
                </div>
            </div>
        </div>



    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.php"; ?>
</body>

</html>