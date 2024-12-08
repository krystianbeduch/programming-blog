<?php
//session_destroy();
//include "bbcode.php";
?>
<script src="../js/add-comment-form-validation.js"></script>
<script src="../js/add-comment-bbcode.js"></script>

<form id="add-comment-form" class="add-form" name="add_comment_form" action="../pages/add-comment-preview.php?postId=<?php echo $postId ?? "" ?>" method="post">
<!--    http://www.tomaszx.pl/materialy/test_przesylania.php-->
    <fieldset>
        <legend>Dodaj komentarz</legend>

        <input type="hidden" name="url" value="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<!--        <input type="hidden" name="action" value="addComment">-->
<!--        --><?php //echo $_SESSION["formData"][$postId]["action"]; ?>
        <label for="post-id">Numer postu:</label>
        <input type="text" name="post-id" id="post-id" value="<?php echo $postId ?? ""; ?>" readonly>

        <label for="username">Nazwa użytkownika:</label>
        <?php if (isset($_SESSION["loggedUser"])): ?>
            <input type="text" name="username" id="username" value="<?php echo $_SESSION["loggedUser"]["username"]; ?>" readonly>
        <?php endif ?>

        <?php if (!isset($_SESSION["loggedUser"])): ?>
            <input type="text" name="username" id="username" value="<?php echo $_SESSION["formData"][$postId]["username"] ?? ""; ?>">
        <?php endif ?>

        <span id="username-error" class="error"">

<!--        -->
            <?php echo isset($_SESSION["errors"]["username"]) ? $_SESSION["errors"]["username"] : ""; ?>
<!--        -->
        </span>

        <label for="email">Email:</label>
        <?php if (isset($_SESSION["loggedUser"])): ?>
            <input type="text" name="email" id="email" value="<?php echo $_SESSION["loggedUser"]["email"]?>" readonly>
        <?php endif ?>

        <?php if (!isset($_SESSION["loggedUser"])): ?>
            <input type="text" name="email" id="email" value="<?php echo isset($_SESSION["formData"][$postId]["email"]) ? htmlspecialchars($_SESSION["formData"][$postId]["email"]) : "" ?>">
        <?php endif ?>


        <span id="email-error" class="error">
<!--            -->
            <?php echo $_SESSION["errors"]["email"] ?? ""; ?>
<!--            -->
        </span>

        <label for="content" class="textarea-label">Treść komentarza (obsługuje BBCode):
            <div class="bbcode-info">
                <img src="../images/bbcode-icons/info-solid.svg" alt="info" id="bbcode-img" >
                <!-- Dymek z instrukcją -->
                <div class="bbcode-tooltip-text">
                    Możesz użyć BBCode aby sformatować swój tekst.<br>
                    Zaznacz tekst a następnie kliknij na odpowiedni przycisk.<br>
                    Najedź na przycisk w celu uzyskania szczegółowych informacji.
                </div>
            </div>
        </label>

        <?php include "bbcode.php"; ?>

        <textarea name="content" id="content"><?php echo isset($_SESSION["formData"][$postId]["content"]) ? trim(htmlspecialchars($_SESSION["formData"][$postId]["content"])) : "" ?></textarea>

        <span id="content-error" class="error">
<!--            -->
            <?php echo isset($_SESSION["errors"]["content"]) ? $_SESSION["errors"]["content"] : ""; ?>
<!--            -->
        </span>
        <span id="form-errors" class="error"></span>

        <input type="hidden" name="recaptcha_response" id="recaptcha_response">


        <!-- CAPTCHA -->
        <div id="captcha">
            <?php require_once "captcha.php"; ?>
        </div>

        <button type="submit" class="form-button">Dodaj komentarz</button>
    </fieldset>
</form>

<?php
unset($_SESSION["errors"]);
?>
