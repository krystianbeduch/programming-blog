<?php
//function renderPosts(int $offset, int $commentsPerPage, int $totalComments, array $comments): void {
//    for ($i = $offset; $i < $offset + $commentsPerPage && $i < $totalComments; $i++) {
//        // Docelowo baza
//        echo '<div class="comment">';
//        echo '<h4 class="comment-author">';
//        echo "Autor " . ($i + 1);
//        echo '</h4>';
//        echo '<p class="comment-author-email">';
//        echo "Email " . ($i + 1);
//        echo '</p>';
//        echo '<p class="comment-author-comment">';
//        echo $comments[$i];
//        echo '</p>';
//        echo '</div>';
//    }
//}
function getPaginationData(int $currentPage, int $totalComments, int $commentsPerPage) : array {
    $totalPages = (int) ceil($totalComments / $commentsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $commentsPerPage;

    return [
        'currentPage' => $currentPage,
        'totalPages' => $totalPages,
        'offset' => $offset,
    ];

}
function renderPosts(array $comments) : void {
    foreach ($comments as $index => $comment) {
        echo '<div class="comment">';
        echo '<h4 class="comment-author">';
        echo "Autor " . ($index + 1);
        echo '</h4>';
        echo '<p class="comment-author-email">';
        echo "Email " . ($index + 1);
        echo '</p>';
        echo '<p class="comment-author-comment">';
        echo htmlspecialchars($comment);
        echo '</p>';
        echo '</div>';
    }
}

function renderPagination(int $currentPage, int $totalPages, string $languagePage) : void {
    echo '<nav class="pagination">';
    if ($currentPage > 1) {
        echo '<a href="' . $languagePage . '.php?page=' . ($currentPage - 1) . '">&laquo;</a>';
    }
    echo '<span>Strona ' . $currentPage . ' z ' . $totalPages . '</span>';

    if ($currentPage < $totalPages) {
        echo '<a href="' . $languagePage . '.php?page=' . ($currentPage + 1) . '">&raquo;</a>';
    }
    echo '</nav">';
}






/* Działające-->
<!--    if ($currentPage > 1) {-->
<!--        echo '<a href="' . $languages[$currentPage - 1]['file'] . '">&laquo;</a>';-->
<!--    }-->
<!---->
<!--    if ($currentPage < $totalPages) {-->
<!--        echo '<a href="' . $languages[$currentPage + 1]['file'] . '">&raquo;</a>';-->
<!--    }-->
<!---->
<!---->
<!---->
<!---->
<!--*/
?>



<!--//}-->
<!---->
<!---->
<!--//-->
<!--//    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;-->
<!--//    $max_pages = count($programming_languages);-->
<!--//-->
<!--//-->
<!--//-->
<!--//    if ($current_page > 1) {-->
<!--//        echo '<a href="?page=' . ($current_page - 1) . '">&laquo;</a>';-->
<!--//    }-->
<!--//-->
<!--//    for ($i = 1; $i <= $max_pages; $i++) {}-->
<!--//-->
<!---->
<!---->
<!---->
<!--?>-->