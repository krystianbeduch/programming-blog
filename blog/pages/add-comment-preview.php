<?php
session_start();
//session_destroy();

// Przetwarzanie danych formularza i przechowywanie ich w sesji
$language = $_POST["topic"];
$_SESSION['formData'][$language ] = $_POST;

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

    $text = preg_replace("/\[b](.*?)\[\/b]/s", "<b>$1</b>", $text);
    $text = preg_replace("/\[i](.*?)\[\/i]/s", "<i>$1</i>", $text);
    $text = preg_replace("/\[u](.*?)\[\/u]/s", "<u>$1</u>", $text);
    $text = preg_replace("/\[s](.*?)\[\/s]/s", "<s>$1</s>", $text);
    $text = preg_replace("/\[ul](.*?)\[\/ul]/s", "<ul>$1</ul>", $text);
    $text = preg_replace("/\[li](.*?)\[\/li]/s", "<li>$1</li>", $text);
    $text = preg_replace("/\[quote](.*?)\[\/quote]/s", "<q>$1</q>", $text);
    $text = preg_replace("/\[url=(.*?)](.*?)\[\/url]/s", "<a href='$1' target='_blank'>$2</a>", $text);

    return nl2br($text); // Zamiana nowych linii na <br>
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../images/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../images/favicons/favicon-16x16.png">
    <link rel="manifest" href="../images/favicons/site.webmanifest">

    <!-- Styles -->
    <link rel="stylesheet" href="../css/main.css">
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.php"; ?>

    <section id="main-section" class="add-comment-preview-section">
        <h1>Sprawdź swój komentarz przed dodaniem</h1>
        <p><b>Temat:</b> <?php echo htmlspecialchars($_POST["topic"]); ?></p>
        <p><b>Nickname:</b> <?php echo htmlspecialchars($_POST["nick"]); ?></p>
        <p><b>Email:</b> <?php echo htmlspecialchars($_POST["email"]); ?></p>
        <p><b>Komentarz:</b></p>
        <div class="comment-preview">
            <?php echo convertBBCodeToHTML($_POST['comment']); ?>
        </div>

        <form action="<?php echo $_POST["url"];?>" method="post" style="display: inline;">
            <button type="submit" name="edit" value="1" class="form-button">Cofnij do poprawki</button>
        </form>

        <form action="../comments/test-submit.php" method="post" style="display: inline;">
            <button type="submit" name="confirm" value="1" class="form-button">Zatwierdź</button>
            <?php
            // Przesyłamy dane w ukrytych polach, aby były gotowe do zapisania w bazie
            foreach ($_POST as $key => $value) {
                echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
            }
            ?>
        </form>

    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.php"; ?>
</body>
</html>