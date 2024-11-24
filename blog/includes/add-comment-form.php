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
            <?php
            $random = rand(0, 8);
            $tab = array(
                0 => ["img" => "triangle-red", "text" => "czerwony trójkąt"],
                1 => ["img" => "triangle-green", "text" => "zielony trójkąt"],
                2 => ["img" => "triangle-blue", "text" => "niebieski trójkąt"],
                3 => ["img" => "circle-red", "text" => "czerwone kółko"],
                4 => ["img" => "circle-green", "text" => "zielone kółko"],
                5 => ["img" => "circle-blue", "text" => "niebieskie kółko"],
                6 => ["img" => "square-red", "text" => "czerwony kwadrat"],
                7 => ["img" => "square-green", "text" => "zielony kwadrat"],
                8 => ["img" => "square-blue", "text" => "niebieski kwadrat"],
            );

            // Przypisz prawidlowy indeks
            $correct_img = $tab[$random];
            ?>
            <label>Znajdź <?php echo $tab[$random]["text"];?></label>
            <table>
                <?php
                // Wymieszaj elementy tablicy
                shuffle($tab);

                // Tworzenie tabeli 3x3 z obrazkami
                for ($i = 0; $i < 9; $i++) {
                    if ($i % 3 == 0) {
                        echo "<tr>";
                    }

                    echo "<td><button type='button' class='form-button captcha-button";
                    if ($tab[$i]["img"] == $correct_img["img"]) {
                        // Dodanie klasy poprawnej captchy
                        echo " correct-captcha-button'>";
                    }
                    else {
                        echo "'>";
                    }
                    echo "<img src='../images/captcha/" . $tab[$i]["img"]. ".png' alt='" . $tab[$i]["img"] . "'>";
                    echo "</button></td>";

                    if ($i % 3 == 2) {
                        echo "</tr>";
                    }
                }
                ?>
            </table>
            <span id="captcha-error" class="error"></span>
            <span id="recaptcha-error" class="error">
            <?php echo $_SESSION["errors"]["recaptcha"] ?? ""; ?>
            </span>

        </div>

        <button type="submit" class="form-button">Dodaj komentarz</button>
    </fieldset>
</form>

<?php
unset($_SESSION["errors"]);
?>
