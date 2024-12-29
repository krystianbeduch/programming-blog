<?php
require_once "../classes/DateFilter.php";

function renderPosts(array $posts) : void {
    foreach ($posts as $post) {
        echo "<div class='post'>";
        echo "<h4 class='post-title'>" . $post["title"];
        if (isset($_SESSION["loggedUser"]) && $_SESSION["loggedUser"]["role"] == "Admin") {
            echo "<button class='post-link delete-button' data-post-id='" . $post["post_id"] . "' data-category-name='" . $post["category_name"] . "' title='Usuń post'>";
            echo "<img src='../images/trash-fill.svg' alt='Usuń post'></button>";
        }
        echo "</h4>";
//        renderContent($post);
        echo "<p class='post-author'>Autor: " . $post["username"]. ", " . $post["email"] .
             "<span class='post-date'>Utworzono: " . date("d-m-Y H:i", strtotime($post["created_at"])) .
             "<span class='post-updated'>| Ostatnia aktualizacja: " . date('d-m-Y H:i', strtotime($post["updated_at"])) . "</span></span></p>";
        echo "<p class='post-content'>" . $post["content"] . "</p>";

        // Wyswietlanie zalaczonego zdjecia, jesli istnieje
        if (!empty($post["file_data"]) && str_starts_with($post["file_type"], "image")) {
            $base64Image = base64_encode($post["file_data"]);
            echo "<h5>Załączone zdjęcie:</h5>";
            echo "<img src='data:" . htmlspecialchars($post["file_type"]) . ";base64," . $base64Image . "' alt='Załączone zdjęcie' class='post-attachment'>";
        }
        $comments = getCommentsToPost($post["post_id"]);
        renderCommentsOnMainPage($comments, $post["post_id"]);
        echo "</div>";
    }
}

function renderCommentsOnMainPage(array $comments, int $postId) : void {
    $commentsCount = count($comments);
    echo "<h5>Komentarze: {$commentsCount}</h5>";
    if ($commentsCount >= 1) {
        $comment = $comments[0];
        echo "<div class='comment'>";
        echo "<p class='comment-author'>Autor: " . $comment["username"]. ", " . $comment["email"] .
            "<span class='post-date'>Utworzono: " . date("d-m-Y H:i", strtotime($comment["created_at"])) .
             "</span></p>";
        echo "<p class='comment-author-comment'>";
        echo $comment["content"];
        //        potrzebna funkcja do przekształcania treści komenatrza zgodnie ze znacznikami ??
        echo "</div>";
        echo "</p>";
        if ($commentsCount > 1) {
            echo "<a href='../pages/post.php?postId={$postId}' class='post-comments-link'>Zobacz wszystkie komentarze</a>";
        }
        else if ($commentsCount == 1) {
            echo "<a href='../pages/post.php?postId={$postId}' class='post-comments-link'>Dodaj komentarz</a>";
        }
    }
    else {
        echo "<a href='../pages/post.php?postId={$postId}' class='post-comments-link'>Dodaj komentarz</a>";
    }
}

function renderAllPostComments(array $comments) : void {
    if (count($comments) > 0) {
        foreach ($comments as $comment) {
            echo "<div class='comment'>";
            echo "<p class='comment-author'>Autor: " . $comment["username"]. ", " . $comment["email"] .
                "<span class='post-date'>Utworzono: " . date('d-m-Y H:i', strtotime($comment["created_at"])) .
                "</span></p>";
            echo "<p class='comment-author-comment'>";
            echo $comment["content"];
            echo "</p>";
            if (isset($_SESSION["loggedUser"]) && $_SESSION["loggedUser"]["role"] == "Admin") {
                echo "<button class='post-link delete-button' data-comment-id='" . $comment["comment_id"] . "' title='Usuń komentarz'>";
                echo "<img src='../images/trash-fill.svg' alt='Usuń komentarz'></button>";
            }
            echo "</div>";
        }
    }
    else {
        echo "<p class='comment-author-comment'>Brak komentarzy</p>";
    }
}

function renderPagination(int $currentPage, int $totalPages, string $languagePage) : void {
    $dateFilter = new DateFilter();
    $dateParams = $dateFilter->getDateParams();

    if ($currentPage > 1) {
        echo "<a href='" . $languagePage . ".php?page=" . ($currentPage - 1) . $dateParams . "'>&laquo;</a>";
    }
    echo "<span>Strona " . $currentPage . " z " . $totalPages . "</span>";

    if ($currentPage < $totalPages) {
        echo "<a href='" . $languagePage . ".php?page=" . ($currentPage + 1) . $dateParams . "'>&raquo;</a>";
    }
}

function renderPaginationUserPosts(int $currentPage, int $totalPages) : void {
    // Link do poprzedniej strony
    if ($currentPage > 1) {
        echo "<a href='../pages/management-user-posts.php?page=" . ($currentPage - 1) . "'>&laquo;</a>";
    }

    // Link do 1 strony powyzej 3 strony
    if ($currentPage > 3) {
        echo "<a href='../pages/management-user-posts.php?page=1'>1</a>";
        if ($currentPage > 4) {
            echo "<span>...</span>";
        }
    }

    // Zakres stron wokol aktualnej
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);

    for ($i = $start; $i <= $end; $i++) {
        if ($i == $currentPage) {
            echo "<span class='pagination-active'>" . $i . "</span>"; // Aktualna strona
        }
        else {
            echo "<a href='../pages/management-user-posts.php?page=" . $i . "'>" . $i . "</a>";
        }
    }

    // Ostatnia strona
    if ($currentPage < $totalPages - 2) {
        if ($currentPage < $totalPages - 3) {
            echo "<span>...</span>";
        }
        echo "<a href='../pages/management-user-posts.php?page=" . $totalPages . "'>" . $totalPages . "</a>";
    }

    // Link do nastepnej strony
    if ($currentPage < $totalPages) {
        echo "<a href='../pages/management-user-posts.php?page=" . ($currentPage + 1) . "'>&raquo;</a>";
    }
}

function renderUserPosts(array $userPosts) : void {
    if (count($userPosts) > 0) {
        foreach ($userPosts as $post) {
            echo "<div class='post'>";
            echo "<h4 class='post-title'>" . $post["title"] . "</h4>";
            echo "<span class='post-updated'>Ostatnia aktualizacja: " . date('d-m-Y H:i', strtotime($post["updated_at"])) . "</span>";
//            renderContent($userPosts);
            echo "<p class='post-content'>" . $post["content"] . "</p>";

            if (!empty($post["file_data"]) && str_starts_with($post["file_type"], "image")) {
                // Wyswietlanie zalaczonego zdjecia, jeśsi istnieje
                $base64Image = base64_encode($post["file_data"]);
                echo "<h5>Załączone zdjęcie:</h5>";
                echo "<img src='data:" . htmlspecialchars($post["file_type"]) . ";base64," . $base64Image . "' alt='Załączone zdjęcie' class='post-attachment'>";
            }

            echo "<a href='../pages/post.php?postId=" . $post["post_id"] . "' class='post-comments-link post-link'>Przejdź do strony posta</a>";
            echo "<a href='../pages/edit-post.php?postId=" . $post["post_id"] . "' class='post-comments-link post-link edit-link'>Edytuj</a>";
            echo "<button class='post-comments-link post-link delete-button' data-post-id='" . $post["post_id"] . "'>Usuń</button>";
            echo "</div>";
        }
    }
    else {
        echo "<span class='post-updated'>Brak postów</span>";
    }
}

function renderUserPostsStats(array $userPosts) : void {
//    print_r($userPosts);
    if (count($userPosts) > 0) {
        echo "<table id='user-posts-stats' class='table-stats posts-stats'>";
        echo "<thead>";
        echo "<tr></th><th>Tytuł</th><th>Data aktualizacji</th><th>Data utworzenia</th><th>Komentarze</th><th>Akcje</th></tr>";
        echo "</thead>";
        echo "<tbody>";
        $category = "";
        foreach ($userPosts as $post) {
            echo "<tr>";
            $tmp = $post["category_name"];
            // Zmiana nazwy kategorii dla C# i C++
            $displayCategory = $tmp;
            if ($tmp == "Cpp") {
                $displayCategory = "C++";
            } elseif ($tmp == "Csharp") {
                $displayCategory = "C#";
            }
            if ($category != $tmp) {
                $category = $tmp;
                echo "<tr><th colspan='5'>" . $displayCategory .
                    "<img src='../images/" . $category . "_logo.png' alt='" . $category . "_logo' title='" . $displayCategory . "'></th></tr>";
            }
            echo "<td>" . $post["title"] . "</td>";
            echo "<td>" . date("d-m-Y H:i", strtotime($post["updated_at"]))  . "</td>";
            echo "<td>" . date("d-m-Y H:i", strtotime($post["created_at"]))  . "</td>";
            echo "<td>" . $post["comment_count"] . "</td>";
            echo "<td>";
            echo "<a href='../pages/edit-post.php?postId=" . $post["post_id"] . "' class='post-link edit-link'>";
            echo "<img src='../images/edit.svg' alt='Edytuj'></a>";
            echo "<button class='post-link delete-button' data-post-id='" . $post["post_id"] . "'>";
            echo "<img src='../images/trash-fill.svg' alt='Usuń'></button>";
            echo "</td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    }
}