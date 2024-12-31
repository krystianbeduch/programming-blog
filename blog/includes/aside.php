<?php
require_once "../db/posts-management.php";
$categories = getCategories();

// Pobranie nazwy biezacej strony
$pageName = basename($_SERVER["PHP_SELF"], ".php");

// Wyodrebnienie 'category_name' do zwyklej tablicy
$categoryNames = array_map(function($category) {
    return strtolower($category["category_name"]);
}, $categories);

require_once "../classes/DateFilter.php";
$dateFilter = new DateFilter();
?>

<aside>
    <!-- Panel administracyjny -->
    <?php if (isset($_SESSION["loggedUser"]) && $_SESSION["loggedUser"]["role"] == "Admin"): ?>
        <section>
            <span id="admin-id"><?= $_SESSION["loggedUser"]["id"]; ?></span>
            <h3><a href="../pages/admin-panel.php">Panel administracyjny</a></h3>
        </section>
    <?php endif; ?>

    <!-- O autorze -->
    <?php if (isset($post) && !empty($post["about_me"])): ?>
        <section>
            <h3>O autorze</h3>
            <p><?= $post["about_me"]; ?></p>
        </section>
    <?php endif; ?>

    <!-- Kalendarz -->
    <?php if (in_array($pageName, $categoryNames)): ?>
    <section>
        <h3>Kalendarz</h3>
        <form id="calendar-form" action="../db/mysql-operation.php" method="POST">
            <input type="hidden" name="action" value="getPostsByDate">
            <input type="hidden" name="category" value="<?= $pageName; ?>">

            <label for="start-date">Wyświetl posty z dnia:</label>
            <button class="btn" id="calendar-reset-button" type="button"><img src="../images/x-circle.svg" alt="Reset"></button>
            <input type="text" class="form-control form-control-sm mb-2" id="start-date" placeholder="Data" autocomplete="off" name="start-date" value="<?= $dateFilter->startDate; ?>">

            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="single-date" name="single-date"
                       <?= $dateFilter->hasFilterEndDate() ? "checked" : ""; ?>/>
                <label class="form-check-label" for="single-date">Przedział czasowy:</label>
            </div>

            <div id="end-date-con">
                <input type="text" class="form-control form-control-sm mt-2" id="end-date" placeholder="Data końcowa" autocomplete="off" name="end-date" value="<?= $dateFilter->endDate; ?>">
            </div>

            <div id="button-group" class="input-group">
                <button class="form-button" id="calendar-button" type="button">Wyświetl</button>
            </div>

        </form>
    </section>
    <?php endif; ?>

    <!-- Archiwum -->
    <section>
        <h3>Archiwum</h3>
        <ul>
            <li><a href="../pages/archive.php?month=10.2024">Październik 2024</a></li>
            <li><a href="../pages/archive.php?month=11.2024">Listopad 2024</a></li>
            <li><a href="../pages/archive.php?month=12.2024">Grudzień 2024</a></li>
            <li><a href="../pages/archive.php?month=01.2025">Styczeń 2025</a></li>
        </ul>
    </section>

    <!-- Linki do innych stron -->
    <section>
        <h3>Przydatne zasoby</h3>
        <ul>
            <li><a href="https://www.w3schools.com" target="_blank">W3Schools</a></li>
            <li><a href="https://javastart.pl" target="_blank">Java Start</a></li>
        </ul>       
    </section>

    <!-- Gry -->
    <section>
        <h3><a href="../pages/games.php">Zagraj w gry</a></h3>
    </section>

    <section>
        <h3><a href="../pages/contact-form.php">Formularz kontaktowy</a></h3>
    </section>
</aside>