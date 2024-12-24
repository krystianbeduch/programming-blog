<?php
require_once "modals.php";
?>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../js/user-account.js"></script>

<header>
    <h1><a href="../pages/">Blog programistyczny</a></h1>
    <!-- Ogolne bledy    -->
    <?php if (isset($_SESSION["alert"]["error"])): ?>
        <div class="alert alert-danger">
            <strong>Błąd!</strong>
            <?= $_SESSION["alert"]["error"]; ?>
        </div>
        <?php unset($_SESSION["alert"]["error"]); ?>
    <?php endif; ?>

    <!-- Rejestracja sukces -->
    <?php if (isset($_SESSION["registerAlert"]) && $_SESSION["registerAlert"]): ?>
        <div class="alert alert-success">
            <strong>Zarejestrowano!</strong> Poczekaj na aktywację konta przez administratora.
        </div>
        <?php unset($_SESSION["registerAlert"]); ?>
    <?php endif; ?>

    <!-- Logowanie blad -->
    <?php if (isset($_SESSION["loginAlert"]) && !$_SESSION["loginAlert"]["success"]): ?>
        <div class="alert alert-danger fade show">
            <strong>Błąd!</strong> <?php echo $_SESSION["loginAlert"]["error"] ?>
        </div>
        <?php
        unset($_SESSION["loginAlert"]);
    endif ?>

    <!-- Logowanie sukces -->
    <?php if (isset($_SESSION["loginAlert"]) && $_SESSION["loginAlert"]["success"]): ?>
        <div class="alert alert-success">
            <strong>Zalogowano!</strong> Witaj <?php echo $_SESSION["loggedUser"]["username"] ?>
        </div>
        <?php unset($_SESSION["loginAlert"]); ?>
    <?php endif; ?>

    <!-- Wylogowano -->
    <?php if (isset($_SESSION["logoutAlert"]) && $_SESSION["logoutAlert"]): ?>
        <div class="alert alert-success">
            Wylogowano pomyślnie
        </div>
        <?php unset($_SESSION["logoutAlert"]); ?>
    <?php endif; ?>

    <!-- Zmiany konta poprawne -->
    <?php if (isset($_SESSION["editProfileAlert"]) && $_SESSION["editProfileAlert"]): ?>
        <div class="alert alert-success">
            <strong>Zapisano zmiany!</strong> Zaloguj się ponownie
        </div>
        <?php
        unset($_SESSION["editProfileAlert"]);
    endif ?>

    <!-- Zmiany posta poprawne -->
    <?php if (isset($_SESSION["editPostAlert"]) && $_SESSION["editPostAlert"]): ?>
        <div class="alert alert-success">
            <strong>Zapisano zmiany!</strong> Post zaktualizowany
        </div>
        <?php unset($_SESSION["editPostAlert"]); ?>
    <?php endif; ?>

    <?php if (!isset($_SESSION["loggedUser"])): ?>
    <!-- Przycisk logowania -->
    <a href="#" id="login-link">Zaloguj się</a>
    <?php endif; ?>

    <!-- Sekcja dla zalogowanego uzytkownika -->
    <?php if (isset($_SESSION["loggedUser"])): ?>
    <div id="user-menu">
        <button id="profile-button">
            <span id="logged-user-username"><?php echo $_SESSION["loggedUser"]["username"] ?></span>
            <span class="arrow">▼</span>
        </button>
        <ul id="dropdown-menu">
            <li><a href="../pages/management-user-posts.php">Zarządzaj postami</a></li>
            <li><a href="../pages/edit-profile.php">Edycja profilu</a></li>
            <li><a href="../includes/logout.php">Wyloguj się</a></li>
        </ul>
    </div>
    <?php endif; ?>
</header>