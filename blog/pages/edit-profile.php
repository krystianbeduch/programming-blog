<?php
session_start();

if (!isset($_SESSION["loggedUser"])) {
    http_response_code(401); // Unauthorized - nieuprawniony dostep
    require "../errors/401.html";
    exit;
}

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
    <link rel="stylesheet" href="../css/style-form.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/edit-user-form.js" type="module"></script>
</head>
<body>
    <?php require_once "../includes/header.php"; ?>
<main>
    <?php require_once "../includes/nav.php"; ?>

    <section id="main-section">
        <form id="edit-user-form" class="post-form" action="../db/mysql-operation.php" method="post">
            <fieldset>
                <legend>Edycja profilu</legend>
                <input type="hidden" name="action" value="editUserAccount">
                <label for="id">Numer użytkownika:</label>
                <input type="number" name="id" id="id" value="<?php echo $_SESSION["loggedUser"]["id"]?>" readonly>

                <label for="username">Nazwa użytkownika:</label>
                <button type="button" class="form-button edit-field-form-button" name="username">Zmień</button>
                <button type="button" class="close" name="close-username">Anuluj</button>
                <input type="text" name="username" id="username" value="<?php echo $_SESSION["loggedUser"]["username"]?>" disabled minlength="4">

                <label for="email">Email:</label>
                <button type="button" class="form-button edit-field-form-button" name="email">Zmień</button>
                <button type="button" class="close" name="close-email">Anuluj</button>
                <input type="email" name="email" id="email" value="<?php echo $_SESSION["loggedUser"]["email"]?>" disabled>

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
                <textarea name="about_me" id="about_me" cols="30" rows="10" disabled><?php echo $_SESSION["loggedUser"]["aboutMe"] ?></textarea>
                <button type="submit" class="form-button">Zapisz zmiany</button>
                </fieldset>
            </form>
        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.php"; ?>
</body>

</html>