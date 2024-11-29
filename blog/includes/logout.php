<?php
session_start();
// Usuniecie zmiennej sesyjnej
if (isset($_SESSION["loggedUser"])) {
    unset($_SESSION["loggedUser"]);
}

// Zakonczenie całej sesji
session_destroy();

// Przekierowanie na strone z ktorej nastalo wylogowanie
$redirectUrl = $_SERVER["HTTP_REFERER"] ?? "../pages/";
header("Location: $redirectUrl");
exit;