<script src="../js/add-comment-form-validation.js"></script>
<form id="add-comment-form" name="add_comment_form" action="../comments/test-submit.php" method="post">
<!--    http://www.tomaszx.pl/materialy/test_przesylania.php-->
    <fieldset>
        <legend>Dodaj komentarz</legend>

        <input type="hidden" name="url" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
        <label for="topic">Temat:</label>
        <input type="text" name="topic" id="topic" value="<?php echo $language ?? 'nulll'; ?>" readonly>

        <label for="nick">Nickname:</label>
        <input type="text" name="nick" id="nick" value="<?php isset($_SESSION['form_data']['nick']) ? htmlspecialchars($_SESSION['form_data']['nick']) : '' ?>">
        <span id="nick-error" class="error"">
            <?php echo isset($_SESSION['errors']['nick']) ? $_SESSION['errors']['nick'] : ''; ?>
        </span>

        <label for="email">Email:</label>
        <input type="text" name="email" id="email" value="<?php isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : '' ?>">
        <span id="email-error" class="error"">
            <?php echo isset($_SESSION['errors']['email']) ? $_SESSION['errors']['email'] : ''; ?>
        </span>

        <label for="comment">Treść komentarza:</label>
        <textarea name="comment" id="comment"><?php isset($_SESSION['form_data']['comment']) ? trim(htmlspecialchars($_SESSION['form_data']['comment'])) : '' ?>
        </textarea>
        <span id="comment-error" class="error">
            <?php echo isset($_SESSION['errors']['comment']) ? $_SESSION['errors']['comment'] : ''; ?>
        </span>
        <span id="form-errors" class="error"></span>

        <input type="hidden" name="recaptcha_response" id="recaptcha_response">

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

                    echo "<td><button type='button' class='captcha-button";
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
            <?php echo isset($_SESSION["errors"]["recaptcha"]) ? $_SESSION["errors"]["recaptcha"] : ""; ?>
            </span>

        </div>

        <button type="submit">Dodaj komentarz</button>
    </fieldset>
</form>

<?php
unset($_SESSION['errors']);
?>
