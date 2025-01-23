<?php
require_once "../includes/page-setup.php";
if (!$_GET["month"]) {
    http_response_code(HttpStatus::BAD_REQUEST);
    require "../errors/400.html";
    exit();
}
$month = $_GET["month"];
$pageData = new PageSetup(month: $month);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | Archiwum</title>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section">
        <article id="posts-section">
            <h3>Posty z <?= $month; ?></h3>
            <div class="posts-container">
                <?php renderPosts(array_slice($pageData->posts, $pageData->getOffset(), $pageData->postsPerPage, true)); ?>
            </div>
        </article>

        <nav class="pagination">
            <?php renderPaginationPosts($pageData->getCurrentPage(), $pageData->getTotalPages(), "archive.php?month=$month&"); ?>
        </nav>

    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>