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
//        "A" => [1, 11]
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
    function showDeck(array $deck) : void {
        // Wyswietl karty gracza
        foreach ($deck as $card) {
            $cardName = strtolower($card["name"]);
            $cardColor = strtolower($card["color"]);
            $imagePath = "../images/blackjack/{$cardName}_{$cardColor}.png";
            echo "<img src='$imagePath' alt='{$card["name"]} {$card["color"]}' style='width: 100px; height: auto;'>";
        }
    }

    function croupierDrawCard() : void {
        // Krupier dobiera karty
        while (calculatePoints($_SESSION["croupierDeck"]) < 16) {
            $_SESSION["croupierDeck"][] = drawCard($_SESSION["cardsDeck"]);
        }
    }

    function calculatePoints(array $deck) : int {
        $points = 0;
        foreach ($deck as $card) {
                $points += $card["value"];
        }
        return $points;
    }


    function showGameResults(int $userPoints, int $croupierPoints) : void {
        // Wyświetlanie koncowego wyniku gry
        $finalUserPoints = abs(21 - $userPoints);
        $finalCroupierPoints = abs(21 - $croupierPoints);
        if ($finalUserPoints > $finalCroupierPoints) {
            echo "<p>Krupier wygrał</p>";
        }
        else if ($finalCroupierPoints == $finalUserPoints) {
            echo "<p>Remis</p>";
        }
        else {
            echo "<p>Gracz wygrał</p>";
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
            drawCard($_SESSION["cardsDeck"]),
            drawCard($_SESSION["cardsDeck"])
        ];
        $_SESSION["croupierDeck"] = [
            drawCard($_SESSION["cardsDeck"]),
            drawCard($_SESSION["cardsDeck"])
        ];
    }
    if (isset($_POST["drawCard"])) {
        // Pobierz kolejna karte
        $_SESSION["userDeck"][] = drawCard($_SESSION["cardsDeck"]);
        croupierDrawCard();

    }
    if (isset($_POST["stand"])) {
        croupierDrawCard();
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

        <section id="main-section">
            <h1>Gra BlackJack</h1>
            <p>Zasady</p>
            <ul>
                <li>Za pomocą przycisków gracz może dobierać
                    karty lub spasować.</li>
                <li>Wygrywa ten, kto ma sumę kart bliższą 21</li>
                <li>Krupier
                    <ul>
                        <li>zawsze dobiera kartę, jeśli suma jego kart jest poniżej 16</li>
                        <li>zawsze pasuje, jesli suma jego kart jest większa lub równa 16</li>
                    </ul>
                </li>
            </ul>

            <div id="game">


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
                    for ($i = 0; $i < count($_SESSION["croupierDeck"]); $i++) {
                        echo "<img src='../images/blackjack/back.png' alt='Karta zakryta' style='width: 100px; height: auto;'>";
                    }
                    /* Zakomentuj 2 ponizsze linie aby nie widziec kart i wyniku krupiera */
                    showDeck($_SESSION["croupierDeck"]);
                    echo calculatePoints($_SESSION["croupierDeck"]);
                }
                ?>
                <form action="blackjack.php" method="post">
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