<?php
session_start();
if (!isset($_SESSION["loggedUser"]) && $_SESSION["loggedUser"]["role"] != "Admin") {
    http_response_code(401); // Unauthorized - nieuprawniony dostep
    require "../errors/401.html";
    exit;
}

//if (isset($_GET["postId"]) && is_numeric($_GET["postId"])) {
//    $postId = (int)$_GET["postId"];  // Pobranie postId z URL
//
//    include_once "../db/mysql-operation.php";
//    $post = getOnePostToEdit($_SESSION["loggedUser"]["id"], $postId);
//    if (count($post) == 0 ) {
//        http_response_code(404); // Not Found - nie znaleziono zasobu
//        require "../errors/404.html";
//        exit;
//    }
//}
//else {
//    http_response_code(400); // Bad request - bledna skladnia
//    require "../errors/400.html";
//    exit;
//}
//include_once "../includes/bbcode-functions.php";

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
    <link rel="stylesheet" href="../css/style-admin.css">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="../js/edit-user-post-form.js" type="module"></script>
    <script src="../js/add-comment-bbcode.js"></script>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.php"; ?>

    <section id="main-section">
        <h1>Panel Administracyjny</h1>
        <a href="admin-users-management.php" class="admin-link">Zarządzanie użytkownikami</a>
        <a href="admin-posts-management.php" class="admin-link">Zarządzanie postami</a>
    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.php"; ?>
</body>

</html>