<?php
session_start();

// Dostep do strony mozliwy jest tylko po przeslaniu formularza
if ( !(isset($_POST["post-id"]) && isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["content"])) ) {
    http_response_code(403); // Forbidden
    require "../errors/403.html";
    exit;
}

// Przetwarzanie danych formularza i przechowywanie ich w sesji
$postId = $_POST["post-id"];
$_SESSION["formData"][$postId] = $_POST;

// Funkcja konwersji BBCode na HTML
include_once "../includes/bbcode-functions.php";

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
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section" class="add-comment-preview-section">
        <h2>Sprawdź swój komentarz przed dodaniem</h2>
        <p><b>Numer postu:</b> <?php echo htmlspecialchars($_POST["post-id"]); ?></p>
        <p><b>Nickname:</b> <?php echo htmlspecialchars($_POST["username"]); ?></p>
        <p><b>Email:</b> <?php echo htmlspecialchars($_POST["email"]); ?></p>
        <p><b>Komentarz:</b></p>
        <div class="comment-preview">
            <?php echo convertBBCodeToHTML($_POST["content"]); ?>
        </div>

        <form action="../db/mysql-operation.php" method="post">
            <input type="hidden" name="url" value="<?php echo $_POST['url'] ?>" >
            <button type="submit" name="action" class="form-button" value="editForm">Cofnij do poprawki</button>
            <button type="submit" name="action" class="form-button" value="addComment">Zatwierdź</button>
            <?php
            // Przesylamy dane w ukrytych polach, aby byly gotowe do zapisania w bazie
            foreach ($_POST as $key => $value) {
                if ($key != "content") {
                    echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . htmlspecialchars($value) . "'>";
                }
                else {
                    echo "<input type='hidden' name='" . htmlspecialchars($key) . "' value='" . convertBBCodeToHTML($value) . "'>";
                }
            }
            ?>
        </form>

    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>