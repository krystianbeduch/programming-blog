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
}
else {
    http_response_code(400); // Bad request - bledna skladnia
    require "../errors/400.html";
    exit;
}
include_once "../includes/bbcode-functions.php";

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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="../js/edit-user-post-form.js" type="module"></script>
    <script src="../js/add-comment-bbcode.js"></script>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section">
        <form id="edit-user-post" class="post-form" name="add_post_form" action="../db/mysql-operation.php" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Edycja posta</legend>
                <input type="hidden" name="action" value="editPost">

                <label for="category">Kategoria:</label>
                <input type="text" name="category" id="category" value="<?php echo $post["category_name"];?>" readonly disabled>

                <label for="post-id">Numer posta:</label>
                <input type="text" name="post-id" id="post-id" value="<?php echo $post["post_id"];?>" readonly>

                <label for="title">Tytuł posta:</label>
                <button type="button" class="form-button edit-field-form-button" name="title">Zmień</button>
                <button type="button" class="close" name="close-title">Anuluj</button>
                <input type="text" name="title" id="title" required value="<?php echo $post["title"]?>" disabled>

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
                    <button type="button" class="form-button edit-field-form-button" name="content">Zmień</button>
                    <button type="button" class="form-button preview-button" id="preview-button">Podgląd HTML</button>
                    <button type="button" class="close" name="close-content">Anuluj</button>
                </label>

                <!-- Kontener na podglad -->
                <div id="preview-container" class="preview-container" style="display: none;">
                    <h4>Podgląd treści</h4>
                    <div id="preview-content" class="preview-content"></div>
                    <button type="button" class="close close-preview-button">Zamknij podgląd</button>
                </div>

                <?php include "../includes/bbcode.php"; ?>

                <textarea name="content" id="content" required disabled><?php echo convertHTMLToBBCode($post["content"]);?></textarea>

                <div class="attachment-section">
                    <?php if (!empty($post["file_data"]) && str_starts_with($post["file_type"], "image")) {
                        echo "<p>Obecny załącznik:</p>";
                        $base64Image = base64_encode($post["file_data"]);
                        echo "<img src='data:" . htmlspecialchars($post["file_type"]) . ";base64," . $base64Image . "' alt='Załączone zdjęcie' class='post-attachment'>";
                    }
                    ?>
                    <label for="attachment">Nowy załącznik (tylko zdjęcia):</label>
                    <input type="file" name="attachment" id="attachment" accept="image/*">
                    <input type="hidden" name="attachment-id" value="<?php echo $post["attachment_id"] ?? null ?>" disabled>
                </div>

                <button type="submit" class="form-button">Zapisz zmiany</button>
            </fieldset>
        </form>

    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>
</body>

</html>