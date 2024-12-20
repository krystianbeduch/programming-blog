<?php
require_once "../db/mysql-operation.php";
function renderUsers() : void {
    $users = getUsers();
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>" . $user["user_id"] . "</td>";
        echo "<td>" . $user["username"] . "</td>";
        echo "<td>" . $user["email"] . "</td>";
        $aboutMe = $user["about_me"];
//        if (strlen($aboutMe) > 20) {
//            $trimmed = substr($aboutMe, 0, 20);
//            // Znalezienie ostatniej spacji w zakresie
//            $lastSpace = strrpos($trimmed, " ");
//            if ($lastSpace) {
//                // Przyciecie do ostatniej spacji
//                $aboutMe = substr($trimmed, 0, $lastSpace);
//            }
//            else {
//                // Brak spacji w 20 znakach, zostawiamy surowe przyciecie
//                $aboutMe = $trimmed;
//            }
//            $aboutMe .= "...";
//        }

        echo "<td class='about-me-col' data-about-me='" . $user["about_me"] . "'>" . $aboutMe . "</td>";
        echo "<td>" . $user["posts_count"] . "</td>";
        echo "<td>" . date("d-m-Y H:i", strtotime($user["created_at"])) . "</td>";
        echo "<td>" . date("d-m-Y H:i", strtotime($user["updated_at"])) . "</td>";
        if ($user["is_active"]) {
            $isActive = "Aktywne";
        }
        else {
            $isActive = "Nieaktywne";
        }
        echo "<td>" . $isActive . "</td>";
        echo "<td>" . $user["role_name"]. "</td>";
        echo "<td><button class='post-link edit-link edit-user-button'>";
        echo "<img src='../images/edit.svg' alt='Edytuj'></button>";
        echo "<button class='post-link delete-button' data-user-id='" . $user["user_id"] . "'>";
        echo "<img src='../images/trash-fill.svg' alt='Usuń'></button></td>";
        echo "</tr>";
    }
} // renderUsers()