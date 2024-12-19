<?php
session_start();
//session_destroy();

// Dostep do strony mozliwy jest tylko po przeslaniu formularza
if ( !(isset($_POST["user-id"]) && isset($_POST["title"]) && isset($_POST["content"])) ) {
    http_response_code(403); // Forbidden
    require "../errors/403.html";
    exit;
}

if (isset($_FILES["attachment"]) && $_FILES["attachment"]["error"] == UPLOAD_ERR_OK) {
    // Sprawdzanie rozmiaru pliku (max 5 MB)
    $maxFileSize = 5 * 1024 * 1024; // 5 MB
    if ($_FILES["attachment"]["size"] > $maxFileSize) {
        $_SESSION["alert"]["error"] = "Plik jest za duży. Maksymalny rozmiar to 5 MB.";
        header("Location: add-post.php?category=" . $_POST["category"]);
        exit();
    }

    // Sprawdzanie formatu pliku
    $allowedExtensions = ["jpg", "jpeg", "png", "gif", "bmp", "svg"];
    $fileExtension = strtolower(pathinfo($_FILES["attachment"]["name"], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        $_SESSION["alert"]["error"] = "Nieobsługiwany format pliku. Dozwolone formaty to: JPG, PNG, GIF, BMP, SVG.";
        header("Location: add-post.php?category=" . $_POST["category"]);
        exit();
    }

    // Odczytanie zawartości pliku
    $fileContent = file_get_contents($_FILES["attachment"]["tmp_name"]);

    // Zapisanie danych pliku
    $_SESSION["uploaded_file"] = [
        // Nazwa pliku
        "name" => $_FILES["attachment"]["name"],
        // Typ MIME
        "type" => $_FILES["attachment"]["type"],
        // Rozmiar pliku
        "size" => $_FILES["attachment"]["size"],
        // Rozszerzenie pliku
        "extension" => $fileExtension,
        // Zakodowana zawartość pliku
        "content" => base64_encode($fileContent)
    ];

}


// Przetwarzanie danych formularza i przechowywanie ich w sesji (bez pliku)
$category = $_POST["category"];
$_SESSION["formData"][$category] = $_POST;
// docelowo $_SESSION['formData'][$userId][$category]
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
    <?php require_once "../includes/nav.php"; ?>

    <section id="main-section" class="add-comment-preview-section">
        <h1>Sprawdź swój post przed dodaniem</h1>
        <p>
            <strong>Numer użytkownika: </strong>
            <?php echo $_POST["user-id"];?>
        </p>
        <p>
            <strong>Tytuł posta: </strong>
            <?php echo htmlspecialchars($_POST["title"])?>
        </p>
        <p>
            <strong>Komentarz: </strong>
        </p>
        <div class="comment-preview">
            <?php echo convertBBCodeToHTML($_POST["content"]); ?>
        </div>
        <p>
            <strong>Załącznik: </strong>
            <?php echo $_FILES["attachment"]["name"]; ?>
        </p>

<!--        <form action="--><?php //echo $_POST["url"];?><!--"
 method="post" style="display: inline;">-->
        <form action="../db/mysql-operation.php" method="post" enctype="multipart/form-data">
<!--            <input type="hidden" name="action" value="editForm">-->
            <button type="submit" name="action" class="form-button"  value="editForm">Cofnij do poprawki</button>

        <!--        <form action="../comments/test-submit.php" method="post" style="display: inline;">-->

            <button type="submit" name="action" class="form-button" value="addPost">Zatwierdź</button>
            <?php
            // Przesyłamy dane w ukrytych polach, aby były gotowe do zapisania w bazie
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

<?php require_once "../includes/footer.php"; ?>
</body>
</html>