<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="../images/favicons/apple-icon-57x57.png">
    <link rel="apple-touch-icon" sizes="60x60" href="../images/favicons/apple-icon-60x60.png">
    <link rel="apple-touch-icon" sizes="72x72" href="../images/favicons/apple-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="76x76" href="../images/favicons/apple-icon-76x76.png">
    <link rel="apple-touch-icon" sizes="114x114" href="../images/favicons/apple-icon-114x114.png">
    <link rel="apple-touch-icon" sizes="120x120" href="../images/favicons/apple-icon-120x120.png">
    <link rel="apple-touch-icon" sizes="144x144" href="../images/favicons/apple-icon-144x144.png">
    <link rel="apple-touch-icon" sizes="152x152" href="../images/favicons/apple-icon-152x152.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../images/favicons/apple-icon-180x180.png">
    <link rel="icon" type="image/png" sizes="192x192"  href="../images/favicons/android-icon-192x192.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../images/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="96x96" href="../images/favicons/favicon-96x96.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../images/favicons/favicon-16x16.png">
    <link rel="manifest" href="../images/favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php require_once "../includes/header.php"; ?>

    <main>
        <?php require_once "../includes/nav.php"; ?>

        <section id="main-section">
            <h1>Java</h1>
            <p>
                Wszechstronny, obiektowy język programowania, który jest używany do budowy rozbudowanych aplikacji desktopowych, webowych oraz mobilnych. Jego główną zaletą jest przenośność – kod napisany w Javie może działać na różnych platformach dzięki mechanizmowi JVM (Java Virtual Machine). Java znajduje szerokie zastosowanie w systemach backendowych, gdzie wraz z popularnymi frameworkami, takimi jak Spring czy Hibernate, umożliwia tworzenie skalowalnych i wydajnych aplikacji serwerowych. Dzięki temu jest jednym z najczęściej wybieranych języków w dużych korporacyjnych systemach i rozwiązaniach o wysokiej wydajności.
            </p>
            <img src="../images/java_logo.png" alt="Java logo">

            <br><br><br><br><br>
            <?php
            $article_header = array("wpis1", "wpis2", "wpis3", "wpis4", "wpis5");
            $article_content = array("zawartosc1", "zawartosc2", "zawartosc3", "zawartosc4", "zawartosc5");
            $article_footer = array("stopka1", "stopka2", "stopka3", "stopka4", "stopka5");

            for ($i = 0; $i < count($article_header); $i++) {
                echo "<article class='test-article'>";
                echo "<header>";
                echo $article_header[$i];
                echo "</header>";
                echo "<p>";
                echo $article_content[$i];
                echo "</p>";
                echo "<footer>";
                echo $article_footer[$i];
                echo "</footer>";
                echo "</article>";
            }
            ?>

        </section>

        <?php require_once "../includes/aside.php"; ?>

    </main>

    <?php require_once "../includes/footer.php"; ?>
</body>

</html>