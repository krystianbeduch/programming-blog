<?php
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

function renderPosts(array $posts) : void {
//    require "../classes/Parsedown.php";
//    $parsedown = new Parsedown();
    foreach ($posts as $post) {
        echo "<div class='post'>";
        echo "<h4 class='post-title'>" . $post["title"] . "</h4>";
        echo "<p class='post-author'>Autor: " . $post["nickname"]. ", " . $post["email"] .
             "<span class='post-date'>Utworzono: " . date('d-m-Y H:i', strtotime($post["created_at"])) .
             "<span class='post-updated'>| Ostatnia aktualizacja: " . date('d-m-Y H:i', strtotime($post["updated_at"])) . "</span></span></p>";
        echo "<p class='post-content'>" . $post["content"] . "</p>";

        $comments = getCommentsToPost($post["post_id"]);
        renderCommentsOnMainPage($comments, $post["post_id"]);

        echo "</div>";
    }
}

function renderCommentsOnMainPage(array $comments, int $postId) : void {
    $commentsCount = count($comments);
    echo "<h4>Komentarze: {$commentsCount}</h4>";
    if ($commentsCount >= 1) {
        $comment = $comments[0];
        echo "<div class='comment'>";
        echo "<p class='comment-author'>Autor: " . $comment["nickname"]. ", " . $comment["email"] .
            "<span class='post-date'>Utworzono: " . date('d-m-Y H:i', strtotime($comment["created_at"])) .
             "</span></p>";
        echo "<p class='comment-author-comment'>";
        echo $comment["content"];
        //        potrzebna funkcja do przekształcania treści komenatrza zgodnie ze znacznikami
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
    foreach ($comments as $comment) {
        echo '<div class="comment">';
        echo "<p class='comment-author'>Autor: " . $comment["nickname"]. ", " . $comment["email"] .
            "<span class='post-date'>Utworzono: " . date('d-m-Y H:i', strtotime($comment["created_at"])) .
            "</span></p>";
        echo '<p class="comment-author-comment">';
        echo $comment["content"];
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


function convertContentToHTMLL($text) {
    $text = html_entity_decode($text);
    /*
    \[b] - znacznik [b]
    \[\/b] - znacznik [/b]
    . - dowolny znak wraz ze znakiem nowej linii (ze wzgledu na ustawiona flage s
    * - zero lub wiecej poprzedzajacego elementu (czyli kropki)
    ? - wyrazenie nongreedy - dopasowanie zatrzyma sie na pierwszym wystapieniu [/b]
    (.*?) - cale wyrazenie dopasowuje dowolny tekst miedzy znacznikami, zachowujac ten tekst jako grupe do pozniejszego uzycia jako $1
    */

    $text = preg_replace("/\[b](.*?)\[\/b]/s", "<strong>$1</strong>", $text);
    $text = preg_replace("/\[i](.*?)\[\/i]/s", "<em>$1</em>", $text);
    $text = preg_replace("/\[u](.*?)\[\/u]/s", "<u>$1</u>", $text);
    $text = preg_replace("/\[s](.*?)\[\/s]/s", "<s>$1</s>", $text);
    $text = preg_replace("/\[ul](.*?)\[\/ul]/s", "<ul>$1</ul>", $text);
    $text = preg_replace("/\[li](.*?)\[\/li]/s", "<li>$1</li>", $text);
    $text = preg_replace("/\[quote](.*?)\[\/quote]/s", "<q>$1</q>", $text);
    $text = preg_replace("/\[url=(.*?)](.*?)\[\/url]/s", '<a href="$1" target="_blank">$2</a>', $text);

    return nl2br($text); // Zamiana nowych linii na <br>
}