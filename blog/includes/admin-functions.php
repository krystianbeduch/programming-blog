<?php
require_once "../db/mysql-operation.php";
function renderUsers() : void {
    $users = getUsers_Admin();
    foreach ($users as $user) {
        echo "<tr data-user-id='" . $user["user_id"] . "'>";
        echo "<td>" . $user["user_id"] . "</td>";
        echo "<td class='user-stats-username'>" . $user["username"] . "</td>";
        echo "<td class='user-stats-email'>" . $user["email"] . "</td>";
        echo "<td class='user-stats-about-me about-me-col' data-about-me='" . $user["about_me"] . "'>" . $user["about_me"] . "</td>";
        echo "<td>" . $user["posts_count"] . "</td>";
        echo "<td>" . date("d-m-Y H:i", strtotime($user["created_at"])) . "</td>";
        echo "<td>" . date("d-m-Y H:i", strtotime($user["updated_at"])) . "</td>";
        if ($user["is_active"]) {
            $isActive = "Aktywne";
        }
        else {
            $isActive = "Nieaktywne";
        }
        echo "<td class='is-active-col' data-is-active='" . $user["is_active"] . "'>" . $isActive . "</td>";
        echo "<td class='user-stats-role'>" . $user["role_name"]. "</td>";
        echo "<td><button class='post-link edit-link edit-user-button'>";
        echo "<img src='../images/edit.svg' alt='Edytuj'></button>";
        echo "<button class='post-link delete-button' data-user-id='" . $user["user_id"] . "'>";
        echo "<img src='../images/trash-fill.svg' alt='Usuń'></button></td>";
        echo "</tr>";
    }
} // renderUsers()

function renderPosts_Admin(?string $category = null) : void {
    $posts = getPosts($category);
    foreach ($posts as $post) {
        echo "<tr data-post-id='" . $post["post_id"] . "' data-post-content='" . $post["content"] . "'>";
        echo "<td>" . $post["post_id"] . "</td>";
        echo "<td class='post-category'>" ."<img src='../images/" . $post["category_name"] . "_logo.png' alt='" . $post["category_name"] . "_logo' title='" . $post["category_name"] . "'></td>";
        echo "<td>" . $post["title"] . "</td>";
        echo "<td>" . $post["username"] . " - " . $post["email"] . "</td>";
        echo "<td>" . date("d-m-Y H:i", strtotime($post["created_at"])) . "</td>";
        echo "<td>" . date("d-m-Y H:i", strtotime($post["updated_at"])) . "</td>";
        echo "<td>" . $post["comments_count"] . "</td>";
        echo "<td>";
        echo "<button class='post-link view-button' title='Podgląd treści'><img src='../images/preview.png' alt='Pogląd treści'></button>";

        echo "<a href='../pages/post.php?postId=" . $post["post_id"] . "' class='post-link edit-link view-comments-button' title='Zobacz post'>";
        echo "<img src='../images/view_comments.png' alt='Zobacz post' ></a>";
        echo "<button class='post-link delete-button' data-post-id='" . $post["post_id"] . "' title='Usuń post'>";
        echo "<img src='../images/trash-fill.svg' alt='Usuń'></button>";
        echo "</td>";
        echo "</tr>";
    }
}

function renderFilter(?string $selectedCategory = null) : void {
    $categories = getCategories();
    echo "<form method='GET' id='filterForm'>";
    echo "<select class='form-select' name='category' onchange='document.getElementById(\"filterForm\").submit();'>";
    echo "<option value=''>Wszystkie kategorie</option>";
    foreach ($categories as $category) {
        $displayCategory = $category["category_name"];
        if ($displayCategory == "Cpp") {
            $displayCategory = "C++";
        }
        else if ($displayCategory == "Csharp") {
            $displayCategory = "C#";
        }
        $selected = ($selectedCategory === $category["category_name"]) ? "selected" : "";
        echo "<option value='" . $category["category_name"] . "' $selected>" . $displayCategory . "</option>";
    }
    echo "</select>";
    echo "</form>";
}