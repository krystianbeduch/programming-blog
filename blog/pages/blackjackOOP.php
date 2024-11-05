<?php
require_once "../classes/Card.php";
require_once "../classes/Deck.php";
require_once "../classes/Game.php";
require_once "../classes/Player.php";

use blackjack\Game;

session_start();
//    session_destroy();

if (isset($_POST["reset"])) {
    session_unset();
//    $_SESSION["game"] = null;
    session_destroy();
    header("Location: blackjackOOP.php");
    exit;
}

if (!isset($_SESSION["game"])) {
    $_SESSION["game"] = new Game();
}

if (isset($_POST["drawCard"])) {
    // Sprawdz przed dobraniem ile gracz ma karti
    if ($_SESSION["game"]->getUser()->getDeckCount() < 5) {
        // Pobierz kolejna karte
        $_SESSION["game"]->userDrawCard();
    }
    else {
        // Gracz ma w talii juz maksymalna liczbe (5) kart - koniec gry
        if (isset($_POST["changeAceValue"])) {
            $_SESSION["game"]->getUser()->changeAceValues($_POST["changeAceValue"]);
        }
        $_SESSION["gameOver"] = true;
    }
    $_SESSION["game"]->croupierDrawCard();
}

if (isset($_POST["stand"])) {
    $_SESSION["game"]->croupierDrawCard();
    if (isset($_POST["changeAceValue"])) {
        $_SESSION["game"]->getUser()->changeAceValues($_POST["changeAceValue"]);
    }
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
            <h1>Gra BlackJack OOP</h1>
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
                <form action="blackjackOOP.php" method="post" id="blackjack-form">
                    <h2>Karty gracza:</h2>
                    <?php
                    $_SESSION["game"]->showUserDeck();
                    echo "<p>Punkty gracza: " . $_SESSION["game"]->getUserPoints() . "</p>";
                    echo "<br>";
                    ?>
                    <h2>Karty krupiera:</h2>
                    <?php
                    if (isset($_SESSION["gameOver"]) && $_SESSION["gameOver"]) {
                        $_SESSION["game"]->showCroupierDeck();
                        echo "<p>Punkty krupiera: " . $_SESSION["game"]->getCroupierPoints() . "</p>";
                        $_SESSION["game"]->getGameResults();
                    }
                    else {
                        $firstCard = true;
                        foreach ($_SESSION["game"]->getCroupier()->getDeck() as $card) {
                            if ($firstCard) {
                                $firstCard = false;
                                $imagePath = $card->getImagePath();
                                $cardName = $card->getName();
                                $cardColor = $card->getColor();
                                echo "<img src='" . $card->getImagePath() . "' alt='" . $card->getName() . $card->getColor()."'>";
                            }
                            else {
                                $backCard = $card->getImageOfBackCard();
                                echo "<img src='{$backCard}' alt='Karta zakryta'>";
                            }
                        }
                        /* Zakomentuj 2 ponizsze linie aby nie widziec kart i wyniku krupiera */
//                        echo "<br>" . $_SESSION["game"]->showCroupierDeck();
//                        echo "<p>Punkty krupiera: " . $_SESSION["game"]->getCroupierPoints() . "</p>";
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