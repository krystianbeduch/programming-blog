<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | Drag Racers</title>
    <link rel="stylesheet" href="../css/style-games.css">
    <link rel="stylesheet" href="../css/style-drag-racers.css">
    <script src="../js/drag-racers-game.js"></script>
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