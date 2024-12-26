<script src="../js/add-comment-form-validation.js"></script>
<script src="../js/add-bbcode.js"></script>

<form id="add-comment-form" class="post-form" name="add_comment_form" action="../pages/add-comment-preview.php?postId=<?= $postId ?? ""; ?>" method="post">
    <fieldset>
        <legend>Dodaj komentarz</legend>

        <input type="hidden" name="url" value="<?= $_SERVER["REQUEST_URI"]; ?>">
        <label for="post-id">Numer postu:</label>
        <input type="text" name="post-id" id="post-id" value="<?= $postId ?? ""; ?>" readonly>

        <label for="username">Nazwa użytkownika:</label>
        <?php if (isset($_SESSION["loggedUser"])): ?>
            <input type="text" name="username" id="username" value="<?= $_SESSION["loggedUser"]["username"]; ?>" readonly>
        <?php else: ?>
            <input type="text" name="username" id="username" value="<?= $_SESSION["formData"][$postId]["username"] ?? ""; ?>">
        <?php endif; ?>
        <span id="username-error" class="error"></span>

        <label for="email">Email:</label>
        <?php if (isset($_SESSION["loggedUser"])): ?>
            <input type="text" name="email" id="email" value="<?= $_SESSION["loggedUser"]["email"]; ?>" readonly>

        <?php else: ?>
            <input type="text" name="email" id="email" value="<?= isset($_SESSION["formData"][$postId]["email"]) ? htmlspecialchars($_SESSION["formData"][$postId]["email"]) : ""; ?>">
        <?php endif; ?>
        <span id="email-error" class="error"></span>

        <label for="content" class="textarea-label">Treść komentarza (obsługuje BBCode):
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

        <?php include_once "bbcode.php"; ?>

        <textarea name="content" id="content"><?= isset($_SESSION["formData"][$postId]["content"]) ? trim(htmlspecialchars($_SESSION["formData"][$postId]["content"], ENT_QUOTES | ENT_HTML5)) : "" ?></textarea>
        <span id="content-error" class="error"></span>

        <span id="form-errors" class="error"></span>

        <div id="captcha">
            <?php require_once "captcha.php"; ?>
        </div>

        <button type="submit" class="form-button">Dodaj komentarz</button>
    </fieldset>
</form>