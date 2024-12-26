<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | Snake</title>
    <link rel="stylesheet" href="../css/style-games.css">
    <link rel="stylesheet" href="../css/style-snake.css">
    <script src="../js/snake-game.js"></script>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section" class="game-section">
        <h2>Gra Snake</h2>
        <h5>Zasady:</h5>
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

<?php require_once "../includes/footer.html"; ?>

</body>
</html>