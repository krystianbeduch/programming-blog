<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog | Drag Racers</title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../images/favicons/favicon-16x16.png">
    <link rel="manifest" href="../images/favicons/site.webmanifest">

    <!-- Bootstrap -->
    <link rel="stylesheet" href="../bootstrap-5.3.3-dist/css/bootstrap.min.css">
    <script src="../bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js"></script>

    <!-- Styles -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style-games.css">
    <link rel="stylesheet" href="../css/style-drag-racers.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../js/drag-racers-jQuery.js"></script>
</head>
<body>
    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.html"; ?>

        <section id="main-section" class="drag-section game-section">
            <h2>Gra Drag Racers</h2>
            <h5>Zasady:</h5>
            <ul>
                <li>Celem gry jest dojechanie jako pierwszym na mete</li>
                <li>Postęp jest widoczny na pasku progresu</li>
                <li>Najechanie na przeszkodzę powoduje opóźnienie dotarcia do mety</li>
            </ul>

            <div id="progress-container">
                Samochód 1: <progress id="car1-progress" max="100" value="0"></progress>
                Samochód 2: <progress id="car2-progress" max="100" value="0"></progress>
            </div>
            <div id="drag-racers-game">
                <canvas id="game-canvas" width="400" height="600"></canvas>
            </div>

        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.html"; ?>

</body>
</html>