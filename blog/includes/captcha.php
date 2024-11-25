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