<?php
session_start();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <?php require_once "../includes/head.html"; ?>
    <title>Blog | Formularz kontaktowy</title>
    <script src="../js/contact-form.js"></script>

</head>
<body>
<?php require_once "../includes/header.php"; ?>

<main>
    <?php require_once "../includes/nav.html"; ?>

    <section id="main-section">
        <form id="contact-form" name="contact-form" action="../db/send-email.php" method="post" novalidate>
            <h2>Formularz kontaktowy</h2>

            <div class="row gy-4">
                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="contact-name" placeholder="Enter username" name="contact-name" required minlength="3">
                        <label for="contact-name">Nazwa użytkownika</label>
                        <div class="invalid-tooltip">Podaj nazwę użytkownika</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating">
                        <input type="email" class="form-control" id="contact-email" placeholder="Enter email" name="contact-email" required>
                        <label for="contact-email">Email</label>
                        <div class="invalid-tooltip">Podaj poprawny email</div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-floating">
                        <input type="text" class="form-control" id="contact-subject" placeholder="Enter subject" name="contact-subject" required>
                        <label for="contact-subject">Temat</label>
                        <div class="invalid-tooltip">Podaj temat</div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <textarea name="contact-message" id="contact-message" class="form-control" required></textarea>
                        <label for="contact-message">Wiadomość</label>
                        <div class="invalid-tooltip">Podaj wiadomość</div>
                    </div>
                </div>
            </div>

            <button type="submit" class="form-button">Wyślij wiadomość</button>
        </form>
    </section>

    <?php require_once "../includes/aside.php"; ?>

</main>

<?php require_once "../includes/footer.html"; ?>

</body>
</html>