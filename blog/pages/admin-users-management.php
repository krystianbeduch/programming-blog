<?php
session_start();
require_once "../errors/error-codes.php";

if (!isset($_SESSION["loggedUser"]) || $_SESSION["loggedUser"]["role"] != "Admin") {
    http_response_code(HttpStatus::UNAUTHORIZED);
    require "../errors/401.html";
    exit();
}

require_once "../includes/admin-functions.php";
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | Panel administracyjny</title>
    <link rel="stylesheet" href="../css/style-admin.css">
    <link rel="stylesheet" href="../css/style-table-stats.css">
    <script src="../js/admin-users.js" type="module"></script>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section">
        <h2>Panel Administracyjny - zarządzanie użytkownikami</h2>
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

        <!-- Kontener na podglad 'O mnie' -->
        <div id="preview-container" class="preview-container" style="display: none;">
            <h4>Podgląd "O mnie"</h4>
            <div id="preview-content" class="preview-content"></div>
            <button type="button" class="close close-preview-button">Zamknij podgląd</button>
        </div>

         <!-- Modal zmieniania aktywnosci uzytkownika -->
        <div id="change-user-activity-modal" class="modal change-activity">
            <div class="modal-content">
                <p></p>
                <div class="modal-buttons">
                    <button id="cancel-activity-button" class="modal-button cancel-button">Anuluj</button>
                    <button id="confirm-activity-button" class="modal-button confirm-button">Potwierdź</button>
                </div>
            </div>
        </div>

        <!-- Modal edycji uzytkownika -->
        <div id="edit-user-modal" class="modal edit-modal">
            <div class="modal-content">
                <h4>Edycja użytkownika</h4>
                <form id="edit-user-form" class="needs-validation">
                    <input type="hidden" name="id" id="e-id">

                    <div class="form-floating mb-4 mt-3">
                        <input type="text" class="form-control" id="e-username" placeholder="Enter username" name="username" minlength="4" required>
                        <label for="e-username">Nazwa użytkownika</label>
                        <div class="invalid-tooltip"></div>
                    </div>

                    <div class="form-floating mb-4 mt-4 input-group-sm">
                        <input type="email" class="form-control" id="e-email" placeholder="Enter email" name="email" required>
                        <label for="e-email">Email</label>
                        <div class="invalid-tooltip"></div>
                    </div>

                    <div class="form-floating mb-4 mt-4">
                        <input type="password" class="form-control" id="e-password" placeholder="Enter password" name="password" minlength="6" disabled>
                        <label for="e-password">Hasło</label>
                        <div class="invalid-tooltip"></div>
                    </div>
                    <div class="form-floating mb-2 mt-4">
                        <input type="password" class="form-control" id="e-password-confirm" placeholder="Enter password" name="passwordConfirm" minlength="6" disabled>
                        <label for="e-password-confirm">Potwierdź hasło</label>
                        <div class="invalid-tooltip"></div>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="e-password-change" name="password-change">
                        <label class="form-check-label" for="e-password-change">Zmień hasło</label>
                    </div>

                    <div class="form-floating mb-1 mt-3">
                        <textarea name="aboutMe" id="e-about-me" class="form-control"></textarea>
                        <label for="e-about-me">O mnie</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" id="e-role-user" name="role" value="2">
                        <label class="form-check-label" for="e-role-user">Użytkownik</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" class="form-check-input" id="e-role-admin" name="role" value="1">
                        <label class="form-check-label" for="e-role-admin">Admin</label>
                    </div>
                </form>
                <div class="modal-buttons">
                    <button id="cancel-edit-button" class="modal-button cancel-button">Anuluj</button>
                    <button id="confirm-edit-button" class="modal-button confirm-button" type="submit">Potwierdź</button>
                </div>
            </div>
        </div>

        <!-- Modal usuwania uzytkownika -->
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

<?php require_once "../includes/footer.html"; ?>

</body>
</html>