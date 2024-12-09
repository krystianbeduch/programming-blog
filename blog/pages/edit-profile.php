<?php
session_start();
include "../db/mysql-operation.php";

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
    <script src="../js/edit-user-form.js"></script>
</head>
<body>
    <?php require_once "../includes/header.php"; ?>
<main>
    <?php require_once "../includes/nav.php"; ?>

    <section id="main-section">
        <form id="edit-user-form" class="add-form" name="edit_user_form" action="" method="post">
            <fieldset>
                <legend>Edycja profilu</legend>

                <label for="id">Numer użytkownika:</label>
                <input type="number" name="id" id="id" value="<?php echo $_SESSION["loggedUser"]["id"]?>" readonly>

                <label for="username">Nazwa użytkownika:</label>
                <input type="text" name="username" id="username" value="<?php echo $_SESSION["loggedUser"]["username"]?>" required minlength="6">

                <label for="email">Email:</label>
                <input type="email" name="email" id="email" value="<?php echo $_SESSION["loggedUser"]["email"]?>">

                <fieldset id="edit-password">
                    <legend>Hasło</legend>
                    <label for="current-password">Obecne hasło:</label>
                    <input type="password" name="current-password" id="current-password">

                    <label for="new-password">Nowe hasło:</label>
                    <input type="password" name="new-password" id="new-password">

                    <label for="new-password-confirm">Powtórz nowe hasło:</label>
                    <input type="password" name="new-password-confirm" id="new-password-confirm">
                </fieldset>
                <label for="about-me">O mnie:</label>
                <textarea name="about-me" id="about-me" cols="30" rows="10"><?php echo $_SESSION["loggedUser"]["aboutMe"] ?></textarea>
                <button type="submit" class="form-button">Zapisz zmiany</button>
                </fieldset>
            </form>
        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.php"; ?>
</div>
</body>

</html>