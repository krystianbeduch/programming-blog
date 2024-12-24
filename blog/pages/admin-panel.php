<?php
session_start();
require_once "../errors/error-codes.php";

if (!isset($_SESSION["loggedUser"]) && $_SESSION["loggedUser"]["role"] != "Admin") {
    http_response_code(HttpStatus::UNAUTHORIZED);
    require "../errors/401.html";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog | Panel administracyjny</title>
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
    <link rel="stylesheet" href="../css/style-admin.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>
    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.html"; ?>

        <section id="main-section">
            <h2>Panel Administracyjny</h2>
            <a href="admin-users-management.php" class="admin-link">Zarządzanie użytkownikami</a>
            <a href="admin-posts-management.php" class="admin-link">Zarządzanie postami</a>
        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>