<?php
session_start();
require_once "../errors/error-codes.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(HttpStatus::FORBIDDEN);
    require "../errors/403.html";
    exit();
}

$name = htmlspecialchars($_POST["contact-name"] ?? "", ENT_QUOTES | ENT_HTML5, "UTF-8");
$email = filter_input(INPUT_POST, "contact-email", FILTER_VALIDATE_EMAIL);
$subject = htmlspecialchars($_POST["contact-subject"] ?? "", ENT_QUOTES | ENT_HTML5, "UTF-8");
$message = htmlspecialchars($_POST["contact-message"] ?? "", ENT_QUOTES | ENT_HTML5, "UTF-8");

// Walidacja danych po stronie serwera
if (empty($name) || mb_strlen($name) < 3) {
    $_SESSION["alert"]["error"] = "Nazwa użytkownika jest zbyt krótka.";
    header("Location: ../pages/contact-form.php");
    exit();
}

if (!$email) {
    $_SESSION["alert"]["error"] = "Nieprawidłowy adres e-mail.";
    header("Location: ../pages/contact-form.php");
    exit();
}

if (empty($subject) || empty($message)) {
    $_SESSION["alert"]["error"] = "Temat i wiadomość nie mogą być puste.";
    header("Location: ../pages/contact-form.php");
    exit();
}

// Konfiguracja odbiorcy wiadomosci
$recipient = "beduch_krystian@o2.pl";

// Tworzenie tresci wiadomosci
$emailMessage = <<<TEXT
Wiadomość od: $name <$email>

Temat:
$subject

Treść wiadomości:
$message
TEXT;

// Nagłówki wiadomości
$headers = [
    "From" => $email,
    "Reply-To" => $email,
    "Content-Type" => "text/plain; charset=UTF-8",
];

$formattedHeaders = implode(
    "\r\n",
    array_map(fn($key, $value) => "$key: $value", array_keys($headers), $headers)
);

// Wysyłanie wiadomości
if (mail($recipient, $subject, $emailMessage, $formattedHeaders)) {
    $_SESSION["alert"]["success"] = "Wiadomość została wysłana pomyślnie.";
}
else {
    $_SESSION["alert"]["error"] = "Wystąpił problem podczas wysyłania wiadomości.";
}
header("Location: ../pages/contact-form.php");
exit();