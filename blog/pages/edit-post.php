<?php
session_start();
require_once "../errors/error-codes.php";
require_once "../db/posts-management.php";

if (!isset($_SESSION["loggedUser"])) {
    http_response_code(HttpStatus::UNAUTHORIZED);
    require "../errors/401.html";
    exit();
}

if (isset($_GET["postId"]) && is_numeric($_GET["postId"])) {
    $postId = (int)$_GET["postId"];  // Pobranie postId z URL

    $post = getOnePostToEdit($_SESSION["loggedUser"]["id"], $postId);
    if (count($post) == 0 ) {
        http_response_code(HttpStatus::NOT_FOUND);
        require "../errors/404.html";
        exit();
    }
}
else {
    http_response_code(HttpStatus::BAD_REQUEST);
    require "../errors/400.html";
    exit();
}
include_once "../includes/bbcode-functions.php";
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog</title>
    <script src="../js/add-bbcode.js"></script>
    <script src="../js/edit-user-post-form.js" type="module"></script>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section">
        <form id="edit-user-post" class="post-form" name="add_post_form" action="../includes/forms.php" method="post" enctype="multipart/form-data">
            <fieldset>
                <legend>Edycja posta</legend>
                <input type="hidden" name="url" value="<?= $_SERVER["REQUEST_URI"]; ?>">

                <label for="category">Kategoria:</label>
                <input type="text" name="category" id="category" value="<?= $post["category_name"]; ?>" readonly disabled>

                <label for="post-id">Numer posta:</label>
                <input type="text" name="post-id" id="post-id" value="<?= $post["post_id"]; ?>" readonly>

                <label for="title">Tytuł posta:</label>
                <button type="button" class="form-button edit-field-form-button" name="title">Zmień</button>
                <button type="button" class="close" name="close-title">Anuluj</button>
                <input type="text" name="title" id="title" required value="<?= $post["title"]; ?>" disabled>

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

                <textarea name="content" id="content" required disabled><?= convertHTMLToBBCode($post["content"]); ?></textarea>

                <div class="attachment-section">
                    <?php if (!empty($post["file_data"]) && str_starts_with($post["file_type"], "image")): ?>
                        <p>
                            Obecny załącznik:
                            <button type="submit" id="delete-attachment-button" class="form-button" name="action" value="deleteAttachment">Usuń</button>
                        </p>
                        <?php
                        $base64Image = base64_encode($post["file_data"]);
                        $fileType = htmlspecialchars($post["file_type"]);
                        ?>
                        <img src="data:<?= $fileType; ?>;base64,<?= $base64Image; ?>" alt="Załączone zdjęcie" class="post-attachment">
                    <?php endif; ?>
                    <label for="attachment">Nowy obraz:</label>
                    <input type="file" name="attachment" id="attachment" accept="image/*">
                    <input type="hidden" name="attachment-id" value="<?= $post["attachment_id"] ?? -1; ?>">
                </div>

                <button type="submit" class="form-button" name="action" value="editPost">Zapisz zmiany</button>
            </fieldset>
        </form>

    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>