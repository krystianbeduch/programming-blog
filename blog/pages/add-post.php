<?php
session_start();
require_once "../db/mysql-operation.php";
//require_once "../includes/render-posts.php";
//$currentPage = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

if (isset($_GET['category'])) {
    if (checkCategory($_GET['category'])) {
        $category = $_GET['category'];
    }
    else {
        echo "Nie ma takiej kategorii";
        exit;
    }
}
else {
    echo "Brak kategorii posta.";
    exit;
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

    <!-- Styles   -->
    <link rel="stylesheet" href="../css/main.css">

    <script src="../js/add-post-form-validation.js"></script>
    <script src="../js/add-comment-bbcode.js"></script>
</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.php"; ?>

    <section id="main-section">
        <form id="add-post-form" class="add-form" name="add_post_form" action="add-post-preview.php" method="post">
            <fieldset>
                <legend>Dodaj post</legend>

                <input type="hidden" name="url" value="<?php echo $_SERVER["REQUEST_URI"]; ?>">
                <input type="hidden" name="action" value="addPost">

                <label for="category">Kategoria:</label>
                <input type="text" name="category" id="category" value="<?php echo $category;?>" readonly>

                <label for="user-id">Numer użytkownika:</label>
                <input type="number" name="user-id" id="user-id" value="<?php echo $_SESSION["formData"][$category]["user-id"] ?? ""; ?>">
<!--  readonly , value=$_SESSION["user_id"]-->

<!--                <label for="nick">Nickname:</label>-->
<!--                <input type="text" name="nick" id="nick" value="--><?php //echo $_SESSION["user"]["nick"] ?? ""; ?><!--" required>-->
<!--                <span id="nick-error" class="error">-->
<!--                --><?php //echo isset($_SESSION["errors"]["nick"]) ? $_SESSION["errors"]["nick"] : ""; ?>
<!--                </span>-->
<!---->
<!--                <label for="email">Email:</label>-->
<!--                <input type="email" name="email" id="email" value="--><?php //echo isset($_SESSION["user"]["email"]) ? htmlspecialchars($_SESSION["user"]["email"]) : "" ?><!--" required>-->
<!--                <span id="email-error" class="error">-->
<!--            --><?php //echo $_SESSION["errors"]["email"] ?? ""; ?>
<!--        </span>-->
                <label for="title">Tytuł posta:</label>
                <input type="text" name="title" id="title" required value="<?php echo $_SESSION["formData"][$category]["title"] ?? ""; ?>">
                <span id="title-error" class="error"></span>

                <label for="content" class="textarea-label">Treść posta (obsługuje BBCode):

                    <div class="bbcode-info">
                        <img src="../images/bbcode-icons/info-solid.svg" alt="info" id="bbcode-img" >
                        <!-- Dymek z instrukcją -->
                        <div class="bbcode-tooltip-text">
                            Możesz użyć BBCode aby sformatować swój tekst.<br>
                            Zaznacz tekst a następnie kliknij na odpowiedni przycisk.<br>
                            Najedź na przycisk w celu uzyskania szczegółowych informacji.
                        </div>
                    </div>
                </label>

                <?php include "../includes/bbcode.php"; ?>

                <textarea name="content" id="content" required><?php echo isset($_SESSION["formData"][$category]["content"]) ? trim(htmlspecialchars($_SESSION["formData"][$category]["content"])) : "" ?></textarea>
                <?php //echo isset($_SESSION["formData"][$postId]["comment"]) ? trim(htmlspecialchars($_SESSION["formData"][$postId]["comment"])) : '' ?>
<!--                </textarea>-->

                <span id="content-error" class="error">
<!--                    -->
            <?php echo isset($_SESSION["errors"]["comment"]) ? $_SESSION["errors"]["comment"] : ""; ?>
<!--                    -->
        </span>
                <span id="form-errors" class="error"></span>

                <input type="hidden" name="recaptcha_response" id="recaptcha_response">


                <!-- CAPTCHA -->
                <div id="captcha">
                    <?php
                    $random = rand(0, 8);
                    $tab = array(
                        0 => ["img" => "triangle-red", "text" => "czerwony trójkąt"],
                        1 => ["img" => "triangle-green", "text" => "zielony trójkąt"],
                        2 => ["img" => "triangle-blue", "text" => "niebieski trójkąt"],
                        3 => ["img" => "circle-red", "text" => "czerwone kółko"],
                        4 => ["img" => "circle-green", "text" => "zielone kółko"],
                        5 => ["img" => "circle-blue", "text" => "niebieskie kółko"],
                        6 => ["img" => "square-red", "text" => "czerwony kwadrat"],
                        7 => ["img" => "square-green", "text" => "zielony kwadrat"],
                        8 => ["img" => "square-blue", "text" => "niebieski kwadrat"],
                    );

                    // Przypisz prawidlowy indeks
                    $correct_img = $tab[$random];
                    ?>
                    <label>Znajdź <?php echo $tab[$random]["text"];?></label>
                    <table>
                        <?php
                        // Wymieszaj elementy tablicy
                        shuffle($tab);

                        // Tworzenie tabeli 3x3 z obrazkami
                        for ($i = 0; $i < 9; $i++) {
                            if ($i % 3 == 0) {
                                echo "<tr>";
                            }

                            echo "<td><button type='button' class='form-button captcha-button";
                            if ($tab[$i]["img"] == $correct_img["img"]) {
                                // Dodanie klasy poprawnej captchy
                                echo " correct-captcha-button'>";
                            }
                            else {
                                echo "'>";
                            }
                            echo "<img src='../images/captcha/" . $tab[$i]["img"]. ".png' alt='" . $tab[$i]["img"] . "'>";
                            echo "</button></td>";

                            if ($i % 3 == 2) {
                                echo "</tr>";
                            }
                        }
                        ?>
                    </table>
                    <span id="captcha-error" class="error"></span>
                    <span id="recaptcha-error" class="error">
            <?php echo $_SESSION["errors"]["recaptcha"] ?? ""; ?>
            </span>

                </div>

                <button type="submit" class="form-button">Dodaj komentarz</button>
            </fieldset>
        </form>

        <?php
        unset($_SESSION["errors"]);
        ?>

    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.php"; ?>
</body>

</html>