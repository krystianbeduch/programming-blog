<?php
//session_destroy();
?>
<script src="../js/add-comment-form-validation.js"></script>
<script src="../js/add-comment-bbcode.js"></script>

<form id="add-comment-form" name="add_comment_form" action="../pages/add-comment-preview.php" method="post">
<!--    http://www.tomaszx.pl/materialy/test_przesylania.php-->
    <fieldset>
        <legend>Dodaj komentarz</legend>

        <input type="hidden" name="url" value="<?php echo $_SERVER["REQUEST_URI"]; ?>">
        <label for="topic">Temat:</label>
        <input type="text" name="topic" id="topic" value="<?php echo $language ?? "nulll"; ?>" readonly>

        <label for="nick">Nickname:</label>
        <input type="text" name="nick" id="nick" value="<?php echo $_SESSION["formData"][$language]["nick"] ?? ""; ?>">
        <span id="nick-error" class="error"">
            <?php echo isset($_SESSION["errors"]["nick"]) ? $_SESSION["errors"]["nick"] : ""; ?>
        </span>

        <label for="email">Email:</label>
        <input type="text" name="email" id="email" value="<?php echo isset($_SESSION["formData"][$language]["email"]) ? htmlspecialchars($_SESSION["formData"][$language]["email"]) : "" ?>">
        <span id="email-error" class="error">
            <?php echo $_SESSION["errors"]["email"] ?? ""; ?>
        </span>

        <label for="comment" class="textarea-label">Treść komentarza (obsługuje BBCode):
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

        <!-- BBCode Editor -->
        <div class="bbcode-toolbar">
            <button id="bbcode-add-b-button" class="bbcode-info" type="button">
                <img src="../images/bbcode-icons/bold-solid.svg" alt="bold">
                <!-- Dymek z instrukcją -->
                <div class="bbcode-tooltip-text">
                    Pogrubienie<br>
                    Prawidłowy format: [b]Tekst[/b]
                </div>
            </button>
            <button id="bbcode-add-i-button" class="bbcode-info" type="button">
                <img src="../images/bbcode-icons/italic-solid.svg" alt="italic">
                <!-- Dymek z instrukcją -->
                <div class="bbcode-tooltip-text">
                    Pochylenie<br>
                    Prawidłowy format: [i]Tekst[/i]
                </div>
            </button>
            <button id="bbcode-add-u-button" class="bbcode-info" type="button">
                <img src="../images/bbcode-icons/underline-solid.svg" alt="underline">
                <!-- Dymek z instrukcją -->
                <div class="bbcode-tooltip-text">
                    Podkreślenie<br>
                    Prawidłowy format: [u]Tekst[/u]
                </div>
            </button>
            <button id="bbcode-add-s-button" class="bbcode-info" type="button">
                <img src="../images/bbcode-icons/strikethrough-solid.svg" alt="strikethrough">
                <!-- Dymek z instrukcją -->
                <div class="bbcode-tooltip-text">
                    Przekreślenie<br>
                    Prawidłowy format: [s]Tekst[/s]
                </div>
            </button>
            <button id="bbcode-add-li-button" class="bbcode-info" type="button">
                <img src="../images/bbcode-icons/list-solid.svg" alt="li">
                <!-- Dymek z instrukcją -->
                <div class="bbcode-tooltip-text">
                    Lista<br>
                    Wypisz elementy listy w osobnych linijkach<br>
                    Prawidłowy format: [ul][li]Element1[/li][li]Element2[/li]...[/ul]
                </div>
            </button>
            <button id="bbcode-add-quote-button" class="bbcode-info" type="button">
                <img src="../images/bbcode-icons/quote-right-solid.svg" alt="quote">
                <!-- Dymek z instrukcją -->
                <div class="bbcode-tooltip-text">
                    Cytat<br>
                    Prawidłowy format: [quote]Tekst[/quote]
                </div>
            </button>
            <button id="bbcode-add-link-button" class="bbcode-info" type="button">
                <img src="../images/bbcode-icons/link-solid.svg" alt="link">
                <!-- Dymek z instrukcją -->
                <div class="bbcode-tooltip-text">
                    Link<br>
                    Wprowadź w okienku adres URL w postaci: https://site.com<br>
                    Prawidłowy format: [url=https://site.com]Tekst[/url]
                </div>
            </button>
        </div>

        <textarea name="comment" id="comment"><?php echo isset($_SESSION["formData"][$language]["comment"]) ? trim(htmlspecialchars($_SESSION["formData"][$language]["comment"])) : '' ?></textarea>

        <span id="comment-error" class="error">
            <?php echo isset($_SESSION["errors"]["comment"]) ? $_SESSION["errors"]["comment"] : ""; ?>
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
