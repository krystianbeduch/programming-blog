<?php
require_once "../db/mysql-operation.php";
function renderUsers() : void {
    $users = getUsers();
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