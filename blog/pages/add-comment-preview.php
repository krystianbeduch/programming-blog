<?php
session_start();
require_once "../errors/error-codes.php";

// Dostep do strony mozliwy jest tylko po przeslaniu formularza
if ( !(isset($_POST["post-id"]) && isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["content"])) ) {
    http_response_code(HttpStatus::FORBIDDEN);
    require "../errors/403.html";
    exit();
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
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | Sprawdź komentarz</title>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section" class="add-comment-preview-section">
        <h2>Sprawdź swój komentarz przed dodaniem</h2>
        <p>
            <strong>Numer postu:</strong>
            <?= htmlspecialchars($_POST["post-id"], ENT_QUOTES | ENT_HTML5); ?>
        </p>
        <p>
            <strong>Nickname:</strong>
            <?= htmlspecialchars($_POST["username"], ENT_QUOTES | ENT_HTML5) ?>
        </p>
        <p>
            <strong>Email:</strong>
            <?= htmlspecialchars($_POST["email"], ENT_QUOTES | ENT_HTML5); ?>
        </p>
        <p><strong>Komentarz:</strong></p>
        <div class="comment-preview">
            <?= convertBBCodeToHTML($_POST["content"]); ?>
        </div>

        <form action="../includes/forms.php" method="post">
            <input type="hidden" name="url" value="<?= $_POST["url"] ?>" >
            <button type="submit" name="action" class="form-button" value="correctForm">Cofnij do poprawki</button>
            <button type="submit" name="action" class="form-button" value="addComment">Zatwierdź</button>
            <!-- Przesylamy dane w ukrytych polach, aby byly gotowe do zapisania w bazie -->
            <?php foreach ($_POST as $key => $value): ?>
                <input
                    type="hidden"
                    name="<?= htmlspecialchars($key, ENT_QUOTES | ENT_HTML5) ?>"
                    value="<?= $key == "content" ? convertBBCodeToHTML($value) : htmlspecialchars($value, ENT_QUOTES | ENT_HTML5) ?>">
            <?php endforeach; ?>
        </form>

    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>