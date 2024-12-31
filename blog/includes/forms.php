<?php

use JetBrains\PhpStorm\NoReturn;

require_once "../db/user-management.php";
require_once "../db/posts-management.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (session_status() === PHP_SESSION_NONE) {
        session_start(); // Sprawdzanie, czy sesja jest juz aktywna
    }

    $action = $_POST["action"] ?? null;

    match ($action) {
        // Wyslanie komentarza do bazy
        "addComment" => addCommentToPost($_POST),
        "addPost" => addPost($_POST),
        "correctForm" => correctForm(),
        "registerUser" => createUserAccount($_POST),
        "loginUser" => loginUser($_POST),
        "editUserAccount" => editUserAccount($_POST),
        "editPost" => editPost($_POST),
        "deleteAttachment" => deleteAttachment($_POST),
        "getPostsByDate" => getPostsByDate($_POST),
        default => handleUnknownAction($action)
    };
}

#[NoReturn]
function handleUnknownAction(string $action): void {
    echo "Nieznana akcja: " . $action;
    exit();
}