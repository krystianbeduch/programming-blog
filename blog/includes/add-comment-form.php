<?php
//session_destroy();
//include "bbcode.php";
?>
<script src="../js/add-comment-form-validation.js"></script>
<script src="../js/add-comment-bbcode.js"></script>

<form id="add-comment-form" class="add-form" name="add_comment_form" action="../pages/add-comment-preview.php" method="post">
<!--    http://www.tomaszx.pl/materialy/test_przesylania.php-->
    <fieldset>
        <legend>Dodaj komentarz</legend>

        <input type="hidden" name="url" value="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <input type="hidden" name="action" value="addComment">
        <label for="topic">Numer postu:</label>
        <input type="text" name="post-id" id="post-id" value="<?php echo $postId ?? "null"; ?>" readonly>

        <label for="nick">Nickname:</label>
        <input type="text" name="nick" id="nick" value="<?php echo $_SESSION["formData"][$postId]["nick"] ?? ""; ?>">

        <span id="nick-error" class="error"">

<!--        -->
            <?php echo isset($_SESSION["errors"]["nick"]) ? $_SESSION["errors"]["nick"] : ""; ?>
<!--        -->
        </span>

        <label for="email">Email:</label>
        <input type="text" name="email" id="email" value="<?php echo isset($_SESSION["formData"][$postId]["email"]) ? htmlspecialchars($_SESSION["formData"][$postId]["email"]) : "" ?>">
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
            <?php echo isset($_SESSION["errors"]["comment"]) ? $_SESSION["errors"]["comment"] : ""; ?>
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
