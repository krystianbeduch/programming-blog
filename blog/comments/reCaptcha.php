<?php
    session_start();

    require_once "secret-key.php";
    $response = $_POST['recaptcha_response'];
    $userIP = $_SERVER['REMOTE_ADDR'];
    $_SESSION['form_data'] = $_POST;

    // Wykonaj zapytanie do Google
    $verificationUrl = "https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$response}&remoteip={$userIP}";
    $responseData = file_get_contents($verificationUrl);
    $responseData = json_decode($responseData);

    if ($responseData->success) {
        // Token jest ważny, możesz kontynuować
        header("Location: test-submit.php"); // Przekieruj na stronę, z której przyszedł formularz
        exit();
    }
    else {
        // Token nie jest ważny, obsłuż błąd
        $_SESSION["errors"]["recaptcha"] = "Błędna reCaptcha";

        if (!empty($_SERVER['HTTP_REFERER'])) {
            header("Location: " . $_SERVER['HTTP_REFERER']); // Przekieruj na stronę, z której przyszedł formularz
        }
        exit();
    }
?>