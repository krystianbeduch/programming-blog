<?php
session_start();
require_once "../errors/error-codes.php";

if (!isset($_SESSION["loggedUser"])) {
    http_response_code(HttpStatus::UNAUTHORIZED);
    require "../errors/401.html";
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog</title>
    <script src="../js/edit-user-form.js" type="module"></script>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section">
        <form id="edit-user-form" class="post-form" action="../db/mysql-operation.php" method="post">
            <fieldset>
                <legend>Edycja profilu</legend>
                <input type="hidden" name="action" value="editUserAccount">
                <label for="id">Numer użytkownika:</label>
                <input type="number" name="id" id="id" value="<?= $_SESSION["loggedUser"]["id"]; ?>" readonly>

                <label for="username">Nazwa użytkownika:</label>
                <button type="button" class="form-button edit-field-form-button" name="username">Zmień</button>
                <button type="button" class="close" name="close-username">Anuluj</button>
                <input type="text" name="username" id="username" value="<?= $_SESSION["loggedUser"]["username"]; ?>" disabled minlength="4">

                <label for="email">Email:</label>
                <button type="button" class="form-button edit-field-form-button" name="email">Zmień</button>
                <button type="button" class="close" name="close-email">Anuluj</button>
                <input type="email" name="email" id="email" value="<?= $_SESSION["loggedUser"]["email"]; ?>" disabled>

                <fieldset id="edit-password">
                    <legend>
                        Hasło
                        <button class="form-button edit-field-form-button" name="password">Zmień</button>
                        <button type="button" class="close" name="close-password">Anuluj</button>
                    </legend>
                    <label for="current-password">Obecne hasło:</label>
                    <input type="password" name="current-password" id="current-password" disabled>

                    <label for="new-password">Nowe hasło:</label>
                    <input type="password" name="new-password" id="new-password" minlength="6" disabled placeholder="min. 6 znaków">

                    <label for="new-password-confirm">Powtórz nowe hasło:</label>
                    <input type="password" name="new-password-confirm" id="new-password-confirm" minlength="6" disabled>
                </fieldset>
                <label for="about_me">O mnie:</label>
                <button type="button" class="form-button edit-field-form-button" name="about_me">Zmień</button>
                <button type="button" class="close" name="close-about_me">Anuluj</button>
                <textarea name="about_me" id="about_me" cols="30" rows="10" disabled><?= $_SESSION["loggedUser"]["aboutMe"]; ?></textarea>
                <button type="submit" class="form-button">Zapisz zmiany</button>
            </fieldset>
        </form>
    </section>

    <?php require_once "../includes/aside.php"; ?>

    </main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>