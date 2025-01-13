<?php
session_start();
require_once "../errors/error-codes.php";

if (!isset($_SESSION["loggedUser"]) || !isset($_SESSION["loggedUser"]["role"]) || $_SESSION["loggedUser"]["role"] != "Admin") {
    http_response_code(HttpStatus::UNAUTHORIZED);
    require "../errors/401.html";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | Panel administracyjny</title>
    <link rel="stylesheet" href="../css/style-admin.css">
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