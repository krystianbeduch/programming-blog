<?php
function renderPagination(int $currentPage, int $totalPages, array $languages) : void {

    echo '<nav class="pagination">';

    if ($currentPage > 1) {
        echo '<a href="' . $languages[$currentPage - 1]['file'] . '">&laquo;</a>';
    }

    if ($currentPage < $totalPages) {
        echo '<a href="' . $languages[$currentPage + 1]['file'] . '">&raquo;</a>';
    }

    echo '</nav>';
}


//
//    $current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
//    $max_pages = count($programming_languages);
//
//
//
//    if ($current_page > 1) {
//        echo '<a href="?page=' . ($current_page - 1) . '">&laquo;</a>';
//    }
//
//    for ($i = 1; $i <= $max_pages; $i++) {}
//



?>