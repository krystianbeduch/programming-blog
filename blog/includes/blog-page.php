<?php
//require_once "../db/posts-management.php";
require_once "../includes/page-setup.php";
$pageData = new PageSetup();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | <?php echo $pageData->languageHeader; ?></title>
    <script src="../js/admin-posts.js" type="module"></script>

    <!-- jQuery UI   -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.min.js"></script>
    <script src="../js/calendar.js" type="module"></script>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section">
        <h2><?= $pageData->languageHeader; ?></h2>
        <p><?= getCategoryDescription($pageData->language); ?></p>
        <?= "<img src='../images/" . $pageData->language . "_logo.png' alt='" . $pageData->language . " logo' title='" . $pageData->language . "' class='language-image'>"; ?>

        <?php if (isset($_SESSION["loggedUser"])): ?>
            <a href="../pages/add-post.php?category=<?php echo $pageData->language;?>" class="post-comments-link add-post-link">Dodaj post</a>
        <?php endif ?>

        <article id="posts-section">
            <h3>Posty</h3>
            <div class="posts-container">

                <?php renderPosts(array_slice($pageData->posts, $pageData->getOffset(), $pageData->postsPerPage, true));
                // preserve_keys = true - zachowaj oryginalne klucze tablicy
                ?>
            </div>
        </article>

        <nav class="pagination">
            <?php renderPagination($pageData->getCurrentPage(), $pageData->getTotalPages(), $pageData->language); ?>
        </nav>

    </section>

    <?php require_once "../includes/delete-post-modal.html"; ?>
    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>