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

    <!-- Styles -->
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/style-games.css">

</head>
<body>
    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.php"; ?>

        <section id="main-section" class="games-section">
            <h2>Gry</h2>
            <p>Masz ochotę na chwilę relaksu? Zagraj w jedną z naszych gier i pozwól sobie na odrobinę przyjemności!</p>
            <div class="game-container">
                <div class="game-tile">
                    <h3>BlackJack</h3>
                    <img src="../images/games/blackjack.jpg" alt="blackjack-game">
                    <a href="blackjackOOP.php" class="play-button">Zagraj</a>
                </div>

                <div class="game-tile">
                    <h3>Snake</h3>
                    <img src="../images/games/snake.jpg" alt="snake-game">
                    <a href="snake.php" class="play-button">Zagraj</a>
                </div>

                <div class="game-tile">
                    <h3>Whack A Mole</h3>
                    <img src="../images/games/whack-a-mole.jpg" alt="whack-a-mole-game">
                    <a href="whack-a-mole.php" class="play-button">Zagraj</a>
                </div>

                <div class="game-tile">
                    <h3>Drag Racers</h3>
                    <img src="../images/games/drag-racers.jpg" alt="drag-racers-game">
                    <a href="drag-racers.php" class="play-button">Zagraj</a>
                </div>

                <div class="game-tile">
                    <h3>Nauka angielskiego</h3>
                    <img src="../images/games/drag-racers.jpg" alt="drag-racers-game">
                    <a href="english-words/conf.html" class="play-button">Zagraj</a>
                </div>
            </div>
        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.php"; ?>
</body>

</html>