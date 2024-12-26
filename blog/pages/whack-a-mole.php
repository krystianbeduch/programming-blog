<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | Whack-a-Mole</title>
    <link rel="stylesheet" href="../css/style-games.css">
    <link rel="stylesheet" href="../css/style-whack.css">
    <script src="../js/drag-racers-jQuery.js"></script>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section" class="whack-section game-section">
        <h2>Gra Whack A Mole</h2>
        <h5>Zasady:</h5>
        <ul>
            <li>Ubij jak najwięcej kretów</li>
            <li>Gra trwa 1 minute</li>
            <li>Wybierz 1 z 3 dostępnych poziomów trudności</li>
        </ul>

        <div id="levels-button">
            <button id="whack-level-1" class="whack-level-button hover-style">Poziom 1<br>Kret co 2 sekundy</button>
            <button id="whack-level-2" class="whack-level-button hover-style">Poziom 2<br>Kret co 1 sekunde</button>
            <button id="whack-level-3" class="whack-level-button hover-style">Poziom 3<br>Kret co 0.5 sekundy</button>
        </div>

        <div id="end-game-button">
            <button id="whack-end-game" disabled="disabled">Zakończ gre</button>
        </div>

        <div id="whack-game">
            <p>Czas gry: <span id="game-timer">60</span></p>
            <p>Punkty: <span id="game-points">0</span></p>
            <p id="game-results"></p>
            <table></table>
        </div>

    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>