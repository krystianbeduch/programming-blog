<?php
    session_start();
//    session_destroy();
    function createDeck(): array {
        $colors = ["Pik", "Kier", "Trefl", "Karo"];
        // Wino, Serce, Zoladz, Dzwonek

        $values = [
            "2" => 2,
            "3" => 3,
            "4" => 4,
            "5" => 5,
            "6" => 6,
            "7" => 7,
            "8" => 8,
            "9" => 9,
            "10" => 10,
            "J" => 10,
            "Q" => 10,
            "K" => 10,
            "A" => 11
        ];
        $deck = [];
        foreach ($values as $name => $value) {
            foreach ($colors as $color) {
                $deck[$name][$color] = $value;
            }
        }
        return $deck;
    }

    function drawCard(array &$deck) : array {
//         return["name" => "A", "color" => "Kier", "value" => 11];
        // Wybieramy losowy rodzaj karty
        $cardNames = array_keys($deck);
        $randomCardName = $cardNames[array_rand($cardNames)];

        // Wybieramy losowy kolor dla wybranego rodzaju karty
        $cardColors = array_keys($deck[$randomCardName]);
        $randomColor = $cardColors[array_rand($cardColors)];

        // Pobieramy wartość karty i usuwamy ją z talii
        $cardValue = $deck[$randomCardName][$randomColor];
        unset($deck[$randomCardName][$randomColor]);

        // Jeśli usunięto wszystkie kolory danej karty, usuwamy nazwę karty z tablicy
        if (empty($deck[$randomCardName])) {
            unset($deck[$randomCardName]);
        }

        // Zwracamy losowo dobraną kartę
        return ["name" => $randomCardName, "color" => $randomColor, "value" => $cardValue];
    }

    function getCardImg(array $card) : array {
        $cardName = strtolower($card["name"]);
        $cardColor = strtolower($card["color"]);
        $imagePath = "../images/blackjack/{$cardName}_{$cardColor}.png";
        return ["cardName" => $cardName, "cardColor" => $cardColor, "imagePath" => $imagePath];
    }
    function showDeck(array $deck) : void {
        // Wyswietl karty gracza
        foreach ($deck as $card) {
            $cardImg = getCardImg($card);
            echo "<img src='{$cardImg["imagePath"]}' alt='{$cardImg["cardName"]} {$cardImg["cardColor"]}'>";
        }

        // Sekcja dla checkboxów
        if ($deck == $_SESSION["userDeck"]) {
            echo "<div class='checkbox-container'>";
            foreach ($deck as $index => $card) {
                if ($card["name"] === "A" && $card["value"] == 11) {
                    echo "<label class='ace-checkbox-label'>
                    <input type='checkbox' name='changeAceValue[]' value='{$index}'> Zmień {$card['name']} {$card['color']} na 1
                  </label>";
                }
            }
            echo "</div>";
        }
    }

    function croupierDrawCard() : void {
        // Krupier dobiera karty
        while (calculatePoints($_SESSION["croupierDeck"]) < 16) {
            $newCard = drawCard($_SESSION["cardsDeck"]);
            $_SESSION["croupierDeck"][] = $newCard;
        }
    }

    function calculatePoints(array $deck) : int {
        $points = 0;
        foreach ($deck as $card) {
            $points += $card["value"];
        }
        return $points;
    }

    function updateAceValues(): void {
        // Zmienia wartości wybranych asów na 1, na podstawie zaznaczonych checkboxów
        if (isset($_POST["changeAceValue"])) {
            foreach ($_POST["changeAceValue"] as $aceIndex) {
                if ($_SESSION["userDeck"][$aceIndex]["name"] === "A") {
                    $_SESSION["userDeck"][$aceIndex]["value"] = 1;
                }
            }
        }
    }


    function showGameResults(int $userPoints, int $croupierPoints) : void {
        // Wyświetlanie koncowego wyniku gry
        $finalUserPoints = abs(21 - $userPoints);
        $finalCroupierPoints = abs(21 - $croupierPoints);
        if ($finalUserPoints > $finalCroupierPoints) {
            echo "<p style='color: red'>Krupier wygrał</p>";
        }
        else if ($finalCroupierPoints == $finalUserPoints) {
            echo "<p style='color: #EEAD00'>Remis</p>";
        }
        else {
            echo "<p style='color: #4CAF50'>Gracz wygrał</p>";
        }
    }

    if (isset($_POST["reset"])) {
        session_unset();
        session_destroy();
        header("Location: blackjack.php");
        exit;
    }

    if (!isset($_SESSION["cardsDeck"])) {
        // Tworzymy talie kart
        $_SESSION["cardsDeck"] = createDeck();

        // Pobieramy po 2 losowe karty z talii
        $_SESSION["userDeck"] = [
//                ["name" => "5", "color" => "Kier", "value" => 5],
//            ["name" => "4", "color" => "Kier", "value" => 4]
//            ["name" => "4", "color" => "Pik", "value" => 4],
            drawCard($_SESSION["cardsDeck"]),
            drawCard($_SESSION["cardsDeck"])
        ];
        $_SESSION["croupierDeck"] = [
//            ["name" => "A", "color" => "Kier", "value" => 11],
//            ["name" => "4", "color" => "Pik", "value" => 4],
//           ];
            drawCard($_SESSION["cardsDeck"]),
            drawCard($_SESSION["cardsDeck"])
        ];
    }

    if (isset($_POST["drawCard"])) {
        // Sprawdz przed dobraniem ile gracz ma karti
        if (count($_SESSION["userDeck"]) < 5) {
            // Pobierz kolejna karte
            $_SESSION["userDeck"][] = drawCard($_SESSION["cardsDeck"]);
        }
        else {
            // Gracz ma w talii juz maksymalna liczbe (5) kart - koniec gry
            updateAceValues();
            $_SESSION["gameOver"] = true;
        }
        croupierDrawCard();
    }

    if (isset($_POST["stand"])) {
        croupierDrawCard();
        updateAceValues();
        $_SESSION["gameOver"] = true;
    }
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

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/style-modal.css">
</head>
<body>
<div id="wrapper">  <!--  ??? -->

    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.php"; ?>

        <section id="main-section" class="blackjack-section">
            <h1>Gra BlackJack</h1>
            <h3>Zasady:</h3>
            <ul>
                <li>Za pomocą przycisków gracz może dobierać
                    karty lub spasować.</li>
                <li>Gracz może dobrać do 5 kart</li>
                <li>Wygrywa ten, kto ma sumę kart bliższą 21</li>
                <li>Krupier
                    <ul>
                        <li>zawsze dobiera kartę, jeśli suma jego kart jest poniżej 16</li>
                        <li>zawsze pasuje, jesli suma jego kart jest większa lub równa 16</li>
                    </ul>
                </li>
                <li>Punktacja:
                    <ul>
                        <li>Karty od 2 do 10 mają wartość nominalną</li>
                        <li>Figury są warte 10</li>
                        <li>As może być liczony jako 11 (domyślnie) lub 1 - przed spasowaniem gracz może zmienić wartość swoich asów w talii</li>
                    </ul>
                </li>
            </ul>

            <div id="blackjack-game">
                <form action="blackjack.php" method="post" id="blackjack-form">

                <?php
                echo "<h2>Karty gracza:</h2>";
                showDeck($_SESSION["userDeck"]);
                $userPoints = calculatePoints($_SESSION["userDeck"]);
                echo "<p>Punkty gracza: $userPoints</p>";
                echo "<br>";

                echo "<h2>Karty krupiera:</h2>";
                if (isset($_SESSION["gameOver"])) {
                    showDeck($_SESSION["croupierDeck"]);
                    $croupierPoints = calculatePoints($_SESSION["croupierDeck"]);
                    echo "<p>Punkty krupiera: $croupierPoints</p>";
                    showGameResults($userPoints, $croupierPoints);
                }
                else {
                    $firstCard = true;
                    foreach ($_SESSION["croupierDeck"] as $card) {
                        if ($firstCard) {
                            $firstCard = false;
                            $cardImg = getCardImg($card);
                            echo "<img src='{$cardImg["imagePath"]}' alt='{$cardImg["cardName"]} {$cardImg["cardColor"]}'>";
                        }
                        else {
                            echo "<img src='../images/blackjack/back.png' alt='Karta zakryta'>";
                        }
                    }
                    /* Zakomentuj 2 ponizsze linie aby nie widziec kart i wyniku krupiera */
//                    echo "<br>" . showDeck($_SESSION["croupierDeck"]);
//                    echo "<p>Punkty krupiera: " . calculatePoints($_SESSION["croupierDeck"]) . "</p>";
                }
                ?>
                    <?php if (!isset($_SESSION["gameOver"])): ?>
                        <button type="submit" name="drawCard">Dobierz kartę</button>
                        <button type="submit" name="stand">Pas</button>
                    <?php endif; ?>
                    <button type="submit" name="reset">Restart gry</button>
                </form>
            </div>
        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.php"; ?>
</div>
</body>
</html>