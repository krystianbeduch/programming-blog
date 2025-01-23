<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | Gry</title>
    <link rel="stylesheet" href="../css/style-games.css">
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section" class="games-section">
        <h2>Gry</h2>
        <p>Masz ochotę na chwilę relaksu? Zagraj w jedną z naszych gier i pozwól sobie na odrobinę przyjemności!</p>
        <div class="game-container">
            <div class="game-tile">
                <h3>BlackJack</h3>
                <img src="../images/games/blackjack.jpg" alt="blackjack-game">
                <a href="blackjack.php" class="play-button">Zagraj</a>
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
        </div>
    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>