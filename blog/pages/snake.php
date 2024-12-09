<?php
session_start();
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

    <!-- Styles -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style-snake.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/drag-racers-jQuery.js"></script>
</head>
<body>

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
                <li>W każdej grze pojawia sie 5 losowych przeszkód oznaczonym kolorem czerwonym</li>
                <li>W każdej grze pojawiają 2 teleporty oznaczone kolorem fioletowym - wejście w jeden powoduje wyjście w drugim</li>
                <li>Gre można zastopować klikąjąc przycisk P</li>
                <li>Gre można zakończyć klikając przycisk E</li>
                <li>Gre można zrestartować klikając przycisk R</li>

            </ul>

            <div id="snake-game">
                <canvas id="game-canvas" width="400" height="400"></canvas>
            </div>
            <span id="game-info"></span>
            <table id="snake-scores">
                <caption>Najlepsze wyniki</caption>
                <thead>
                    <tr><th>Gracz</th><th>Punkty</th></tr>
                </thead>
                <tbody></tbody>
            </table>
        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.php"; ?>
</body>
</html>