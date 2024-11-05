<?php
session_start();
// Przetwarzanie danych formularza i przechowywanie ich w sesji
$language = $_POST["topic"];
$_SESSION['formData'][$language ] = $_POST;

// Funkcja konwersji BBCode na HTML
/*
[b]gfgf[/b]
[i]fdfd[/i]
[u]JD[/u]
[url=https://google.pl]Google[/url]
*/
function convertBBCodeToHTML($text) {
    $text = html_entity_decode($text);
    $text = preg_replace("/\[b\](.*?)\[\/b\]/s", "<b>$1</b>", $text);
    $text = preg_replace("/\[i\](.*?)\[\/i\]/s", "<i>$1</i>", $text);
    $text = preg_replace("/\[u\](.*?)\[\/u\]/s", "<u>$1</u>", $text);
    $text = preg_replace("/\[url=(.*?)\](.*?)\[\/url\]/s", "<a href='$1' target='_blank'>$2</a>", $text);

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

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Podgląd komentarza</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<h1>Podgląd komentarza</h1>
<p><b>Temat:</b> <?php echo htmlspecialchars($_POST['topic']); ?></p>
<p><b>Nickname:</b> <?php echo htmlspecialchars($_POST['nick']); ?></p>
<p><b>Email:</b> <?php echo htmlspecialchars($_POST['email']); ?></p>
<p><b>Komentarz:</b></p>
<div class="comment-preview">
    <?php echo $commentHTML; ?>
</div>

<form action="<?php echo $_POST["url"];?>" method="post" style="display: inline;">
    <button type="submit" name="edit" value="1">Cofnij do poprawki</button>
</form>

<form action="test-submit.php" method="post" style="display: inline;">
    <button type="submit" name="confirm" value="1">Zatwierdź</button>
    <?php
    // Przesyłamy dane w ukrytych polach, aby były gotowe do zapisania w bazie
    foreach ($_POST as $key => $value) {
        echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
    }
    ?>
</form>
</body>
</html>