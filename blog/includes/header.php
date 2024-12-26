<?php
require_once "modals.php";
?>

<header>
    <h1><a href="../pages/">Blog programistyczny</a></h1>
    <!-- Bledy -->
    <?php if (isset($_SESSION["alert"]["error"])): ?>
        <div class="alert alert-danger">
            <strong>Błąd!</strong>
            <?= $_SESSION["alert"]["error"]; ?>
        </div>
        <?php unset($_SESSION["alert"]["error"]); ?>
    <?php endif; ?>

    <!-- Sukces -->
    <?php if (isset($_SESSION["alert"]["success"])): ?>
        <div class="alert alert-success">
            <strong><?= $_SESSION["alert"]["successStrong"] ?? "Sukces!"; ?></strong> <?= $_SESSION["alert"]["success"]; ?>
        </div>
        <?php unset($_SESSION["alert"]["success"]); ?>
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