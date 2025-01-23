<?php
require_once "../classes/blackjack/Game.php";
require_once "../classes/blackjack/Deck.php";
require_once "../classes/blackjack/Card.php";
require_once "../classes/blackjack/Player.php";
require_once "../classes/blackjack/User.php";
require_once "../classes/blackjack/Croupier.php";

use blackjack\blackjack\Game;

if (!class_exists("blackjack\blackjack\Game")) {
    die("Klasa Game nie została załadowana!");
}

session_start();

if (isset($_POST["reset"])) {
    unset($_SESSION["game"]);
    header("Location: blackjack.php");
    exit();
}

if (!isset($_SESSION["game"])) {
    $_SESSION["game"] = Game::getInstance();
}

if (isset($_POST["drawCard"])) {
    // Sprawdz przed dobraniem, ile kart ma gracz
    if ($_SESSION["game"]->getUser()->getDeckCount() < 5) {
        // Pobierz kolejna karte
        $deck = $_SESSION["game"]->getDeck();
        $_SESSION["game"]->getUser()->drawCard($deck);
    }
    else {
        // Gracz ma w talii juz maksymalna liczbe (5) kart - koniec gry
        if (isset($_POST["changeAceValue"])) {
            $_SESSION["game"]->getUser()->changeAceValues($_POST["changeAceValue"]);
        }
        $_SESSION["game"]->setIsGameOver(true);
    }
    // Krupier dobiera karte
    $deck = $_SESSION["game"]->getDeck();
    $_SESSION["game"]->getCroupier()->croupierDrawCard($deck);
}

if (isset($_POST["stand"])) {
    // Krupier dobiera karte
    $deck = $_SESSION["game"]->getDeck();
    $_SESSION["game"]->getCroupier()->croupierDrawCard($deck);

    // Zmiana wartosci asow, jesli uzytkownik wybral taka opcje
    if (isset($_POST["changeAceValue"])) {
        $_SESSION["game"]->getUser()->changeAceValues($_POST["changeAceValue"]);
    }
    $_SESSION["game"]->setIsGameOver(true);
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | Blackjack</title>
    <link rel="stylesheet" href="../css/style-games.css">
    <link rel="stylesheet" href="../css/style-blackjack.css">
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section" class="blackjack-section game-section">
        <h2>Gra BlackJack</h2>
        <h5>Zasady:</h5>
        <ul>
            <li>Za pomocą przycisków gracz może dobierać
                karty lub spasować.</li>
            <li>Gracz może dobrać do 5 kart</li>
            <li>Wygrywa ten, kto ma sumę kart bliższą 21</li>
            <li>Krupier
                <ul>
                    <li>zawsze dobiera kartę, jeśli suma jego kart jest poniżej 16</li>
                    <li>zawsze pasuje, jeśli suma jego kart jest większa lub równa 16</li>
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
                <h4>Karty gracza:</h4>
                <?php
                $_SESSION["game"]->getUser()->showDeck();
                echo "<p>Punkty gracza: " . $_SESSION["game"]->getUser()->getPoints() . "</p>";
                echo "<br>";
                ?>
                <h4>Karty krupiera:</h4>
                <?php
                if ($_SESSION["game"]->getIsGameOver()) {
                    $_SESSION["game"]->getCroupier()->showDeck();
                    echo "<p>Punkty krupiera: " . $_SESSION["game"]->getCroupier()->getPoints() . "</p>";
                    $_SESSION["game"]->getGameResults();
                }
                else {
                    $firstCard = true;
                    foreach ($_SESSION["game"]->getCroupier()->getDeck() as $card) {
                        if ($firstCard) {
                            $firstCard = false;
                            echo "<img src='{$card->getImagePath()}' alt='{$card->getName()} {$card->getColor()}' title='{$card->getName()} {$card->getColor()}'>";
                        }
                        else {
                            echo "<img src='{$card->getImageOfBackCard()}' alt='Karta zakryta' title='Karta zakryta'>";
                        }
                    }
                    /* Comment out the 2 lines below to not see the cards and the dealer's score */
//                        echo "<br>" . $_SESSION["game"]->getCroupier()->showDeck();
//                        echo "<p>Punkty krupiera: " . $_SESSION["game"]->getCroupier()->getPoints() . "</p>";
                }
                ?>
                <?php if (!$_SESSION["game"]->getIsGameOver()): ?>
                    <button type="submit" name="drawCard">Dobierz kartę</button>
                    <button type="submit" name="stand">Pas</button>
                <?php endif; ?>
                <button type="submit" name="reset">Restart gry</button>
            </form>
        </div>
    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>
</body>
</html>