<?php
session_start();
if (!isset($_SESSION["loggedUser"])) {
    http_response_code(401); // Unauthorized - nieuprawniony dostep
    require "../errors/401.html";
    exit;
}

if (isset($_GET["postId"]) && is_numeric($_GET["postId"])) {
    $postId = (int)$_GET["postId"];  // Pobranie postId z URL

    include_once "../db/mysql-operation.php";
    $post = getOnePostToEdit($_SESSION["loggedUser"]["id"], $postId);
    if (count($post) == 0 ) {
        http_response_code(404); // Not Found - nie znaleziono zasobu
        require "../errors/404.html";
        exit;
    }
//    $comments = getCommentsToPost($postId);
}
else {
    http_response_code(400); // Bad request - bledna skladnia
    require "../errors/400.html";
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

    <!-- Styles -->
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
        <form id="edit-user-post" class="post-form" name="add_post_form" action="../db/mysql-operation.php" method="post">
            <fieldset>
                <legend>Edycja posta</legend>

                <input type="hidden" name="url" value="<?php echo $_SERVER["REQUEST_URI"]; ?>">

                <label for="category">Kategoria:</label>
                <input type="text" name="category" id="category" value="<?php echo $post["category_name"];?>" readonly>

                <label for="post-id">Numer posta:</label>
                <input type="text" name="post-id" id="post-id" value="<?php echo $post["post_id"];?>" readonly>

                <label for="title">Tytuł posta:</label>
                <button class="form-button edit-field-form-button" name="title">Zmień</button>
                <input type="text" name="title" id="title" required value="<?php echo $post["title"]?>">

                <span id="title-error" class="error"></span>

                <label for="content" class="textarea-label">Treść posta (obsługuje BBCode):
                    <div class="bbcode-info">
                        <img src="../images/bbcode-icons/info-solid.svg" alt="info" id="bbcode-img" >
                        <!-- Dymek z instrukcją -->
                        <div class="bbcode-tooltip-text">
                            Możesz użyć BBCode aby sformatować swój tekst.<br>
                            Zaznacz tekst a następnie kliknij na odpowiedni przycisk.<br>
                            Najedź na przycisk w celu uzyskania szczegółowych informacji.
                        </div>
                    </div>
                    <button class="form-button edit-field-form-button" name="title">Zmień</button>
                </label>

                <?php include "../includes/bbcode.php"; ?>

                <textarea name="content" id="content" required><?php echo $post["content"] ?></textarea>
                <?php //echo isset($_SESSION["formData"][$postId]["comment"]) ? trim(htmlspecialchars($_SESSION["formData"][$postId]["comment"])) : '' ?>
                <!--                </textarea>-->

<!--                <span id="content-error" class="error">-->
<!--                    -->
<!--            --><?php //echo isset($_SESSION["errors"]["content"]) ? $_SESSION["errors"]["content"] : ""; ?>
<!--                    -->
<!--        </span>-->
<!--                <span id="form-errors" class="error"></span>-->

<!--                <input type="hidden" name="recaptcha_response" id="recaptcha_response">-->


                <!-- CAPTCHA -->
<!--                <div id="captcha">-->
<!--                    --><?php //require_once "../includes/captcha.php"; ?>
<!--                </div>-->

                <button type="submit" class="form-button">Zapisz zmiany</button>
            </fieldset>
        </form>

        <?php
//        unset($_SESSION["errors"]);
        ?>
    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.php"; ?>
</body>

</html>