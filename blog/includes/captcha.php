<?php
$randomIndex = rand(0, 8);
$shapes =  [
    ["img" => "triangle-red", "text" => "czerwony trójkąt"],
    ["img" => "triangle-green", "text" => "zielony trójkąt"],
    ["img" => "triangle-blue", "text" => "niebieski trójkąt"],
    ["img" => "circle-red", "text" => "czerwone kółko"],
    ["img" => "circle-green", "text" => "zielone kółko"],
    ["img" => "circle-blue", "text" => "niebieskie kółko"],
    ["img" => "square-red", "text" => "czerwony kwadrat"],
    ["img" => "square-green", "text" => "zielony kwadrat"],
    ["img" => "square-blue", "text" => "niebieski kwadrat"],
];

// Przypisz prawidlowy indeks
$correctShape = $shapes[$randomIndex];
?>

<label>Znajdź <?= $shapes[$randomIndex]["text"]; ?></label>
<table>
    <?php
    // Wymieszaj elementy tablicy
    shuffle($shapes);

    // Tworzenie tabeli 3x3 z obrazkami
    foreach ($shapes as $index => $shape) {
        if ($index % 3 == 0) {
            echo "<tr>";
        }

        echo "<td><button type='button' class='form-button captcha-button";

        if ($shape["img"] == $correctShape["img"]) {
            // Dodanie klasy poprawnej captchy
            echo " correct-captcha-button'>";
        }
        else {
            echo "'>";
        }
        echo "<img src='../images/captcha/{$shape['img']}.png' alt='{$shape['img']}'>";
        echo "</button></td>";

        if ($index % 3 == 2) {
            echo "</tr>";
        }
    }
    ?>
</table>
<span id="captcha-error" class="error"></span>