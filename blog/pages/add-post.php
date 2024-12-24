<?php
session_start();
require_once "../db/mysql-operation.php";
require_once "../errors/error-codes.php";

if (!isset($_SESSION["loggedUser"])) {
    http_response_code(HttpStatus::UNAUTHORIZED);
    require "../errors/401.html";
    exit();
}

if (!isset($_GET["category"])) {
    http_response_code(HttpStatus::BAD_REQUEST);
    require "../errors/400.html";
    exit();
}

if (!checkCategory($_GET["category"])) {
    http_response_code(HttpStatus::NOT_FOUND);
    require "../errors/404.html";
    exit();
}

$category = $_GET["category"];
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog | Dodaj post</title>
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

    <!-- JavaScript Scripts -->
    <script src="../js/add-comment-bbcode.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/add-post-form-validation.js"></script>

</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section">
        <form id="add-post-form" class="post-form" name="add-post-form" action="add-post-preview.php" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Dodaj post</legend>

                <input type="hidden" name="url" value="<?= $_SERVER["REQUEST_URI"]; ?>">

                <label for="category">Kategoria:</label>
                <input type="text" name="category" id="category" value="<?= $category;?>" readonly>

                <label for="user-id">Numer użytkownika:</label>
                <input type="number" name="user-id" id="user-id" value="<?= $_SESSION["loggedUser"]["id"]; ?>" readonly>

                <label for="title">Tytuł posta:</label>
                <input type="text" name="title" id="title" required value="<?= $_SESSION["formData"][$category]["title"] ?? ""; ?>" data-polish-name="Tytuł posta">
                <span id="title-error" class="error"></span>

                <label for="content" class="textarea-label">Treść posta (obsługuje BBCode):
                    <div class="bbcode-info">
                        <img src="../images/bbcode-icons/info-solid.svg" alt="info" id="bbcode-img" >
                        <!-- Dymek z instrukcja -->
                        <div class="bbcode-tooltip-text">
                            Możesz użyć BBCode aby sformatować swój tekst.<br>
                            Zaznacz tekst a następnie kliknij na odpowiedni przycisk.<br>
                            Najedź na przycisk w celu uzyskania szczegółowych informacji.
                        </div>
                    </div>
                </label>

                <?php include_once "../includes/bbcode.php"; ?>

                <textarea name="content" id="content" required data-polish-name="Treść posta"><?php echo isset($_SESSION["formData"][$category]["content"]) ? trim(htmlspecialchars($_SESSION["formData"][$category]["content"])) : "" ?></textarea>

                <span id="content-error" class="error"></span>
                <span id="form-errors" class="error"></span>

                <label for="attachment">Dodaj obraz:</label>
                <input type="file" name="attachment" id="attachment" accept="image/*">

                <!-- Google Captcha - does not work correctly on localhost
                <input type="hidden" name="recaptcha_response" id="recaptcha_response">
                -->

                <!-- CAPTCHA -->
                <div id="captcha">
                   <?php require_once "../includes/captcha.php"; ?>
                </div>

                <button type="submit" class="form-button">Dodaj post</button>
            </fieldset>
        </form>

    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>