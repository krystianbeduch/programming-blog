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

    <!-- Styles   -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style-snake.css">

    <script src="../js/jquery-3.7.1.min.js"></script>
    <script src="../js/snake-jQuery.js"></script>
</head>
<body>
<div id="wrapper">  <!--  ??? -->

    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.php"; ?>

        <section id="main-section" class="blackjack-section">
            <h1>Gra Snake</h1>
            <h3>Zasady:</h3>
            <ul>
                <li>Poruszanie odbywa się za pomocą strzałek.</li>
                <li>Jedzenie pojawia się w losowym miejscu planszy</li>
                <li>Są 2 rodzaje jedzenia:
                    <ul>
                        <li>Niebieskie - 1 punkt</li>
                        <li>Złote - 3 punkty - pojawia się w różnych odstępach czasowych</li>
                    </ul>
                </li>
                <li>Po każdym posiłku wąż rośnie</li>
                <li>Z każdym punktem zwiększa się szybkość gry</li>
                <li>W każdej grze pojawią się inne przeszkody</li>
                <li>Gre można zastopować klikąjąc przycisk P</li>
                <li>Gre można zakończyć klikając przycisk E</li>
                <li>Gre można zrestartować klikając przycisk E</li>

            </ul>

            <div id="snake-game">
                <canvas id="game-canvas" width="400" height="400"></canvas>

            </div>
            <span id="game-info"></span>
        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.php"; ?>
</div>
</body>
</html>