<?php
require_once "../includes/page-setup.php";
$pageData = new PageSetup();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog | <?php echo $pageData->languageHeader; ?></title>
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

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!--    <script src="../js/user-account.js"></script>-->
    <script src="../js/admin-posts.js" type="module"></script>
</head>
<body>
    <?php
    require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.html"; ?>

        <section id="main-section">
            <h2><?php echo $pageData->languageHeader; ?></h2>
            <p><?php echo getCategoryDescription($pageData->language); ?></p>
            <?php echo "<img src='../images/" . $pageData->language . "_logo.png' alt='" . $pageData->language . " logo' title='" . $pageData->language . "' class='language-image'>"; ?>

            <?php include_once "../includes/post-alerts.php"; ?>

            <?php if (isset($_SESSION["loggedUser"])): ?>
                <a href="../pages/add-post.php?category=<?php echo $pageData->language;?>" class="post-comments-link add-post-link">Dodaj post</a>
            <?php endif ?>

            <article id="comments-section">
                <h3>Posty</h3>
                <div class="comment-container">

                    <?php
                    renderPosts(array_slice($pageData->posts, $pageData->getOffset(), $pageData->postsPerPage, true));
                    // preserve_keys = true - zachowaj oryginalne klucze tablicy
                    ?>
                </div>
            </article>

            <?php renderPagination($pageData->getCurrentPage(), $pageData->getTotalPages(), $pageData->language); ?>

        </section>

        <?php require_once "../includes/delete-post-modal.html"; ?>
        <?php require_once "../includes/aside.php"; ?>

    </main>
    <?php require_once "../includes/footer.html"; ?>
</body>
</html>