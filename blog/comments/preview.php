<?php
//session_start();
// Przetwarzanie danych formularza i przechowywanie ich w sesji
$language = $_POST["topic"];
$_SESSION['formData'][$language ] = $_POST;


/*
[b]gfgf[/b]
[i]fdfd[/i]
[u]JD[/u]
[url=https://google.pl]Google[/url]
*/

// Funkcja konwersji BBCode na HTML
function convertBBCodeToHTML($text) {
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
    $text = preg_replace("/\[quote](.*?)\[\/quote]/s", "<q>$1</q>", $text);
    $text = preg_replace("/\[url=(.*?)](.*?)\[\/url]/s", "<a href='$1' target='_blank'>$2</a>", $text);

    return nl2br($text); // Zamiana nowych linii na <br>
}

// Walidacja danych
$errors = [];
if (empty($_POST['nick'])) {
    $errors['nick'] = 'Nick jest wymagany.';
}
if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Wprowadź poprawny adres email.';
}
if (empty($_POST['comment'])) {
    $errors['comment'] = 'Treść komentarza jest wymagana.';
}

// Jeśli są błędy, zapisz je i przekieruj z powrotem do formularza
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header("Location: " . $_POST["url"]); // Wróć do strony formularza
    exit();
}

// Konwersja komentarza BBCode na HTML
$commentHTML = convertBBCodeToHTML($_POST['comment']);
?>