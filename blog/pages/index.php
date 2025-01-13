<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | Strona Główna</title>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section">
        <h2>Witam na blogu</h2>
        <p>
            Na tym blogu znajdziesz informacje o różnych językach programowania. Dowiesz się, jakie są ich zalety, wady oraz zastosowania. Blog jest podzielony na różne grupy języków, dzięki czemu łatwo znajdziesz interesujące Cię tematy.
        </p>
    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>