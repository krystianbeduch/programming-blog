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

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

    <!-- Styles   -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style-whack.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/whack-jQuery.js"></script>

</head>
<body>
    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.php"; ?>

        <section id="main-section" class="whack-section">
            <h2>Gra Whack A Mole</h2>
            <h3>Zasady:</h3>
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

    <?php require_once "../includes/footer.php"; ?>
</body>
</html>