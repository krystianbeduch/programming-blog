<?php
session_start();
// Usuniecie zmiennej sesyjnej
if (isset($_SESSION["loggedUser"])) {
    unset($_SESSION["loggedUser"]);
}

// Zakonczenie całej sesji
//session_destroy();
$_SESSION["logoutAlert"] = true;

// Przekierowanie na strone z ktorej nastapilo wylogowanie
//$redirectUrl = $_SERVER["HTTP_REFERER"] ?? "../pages/";
$redirectUrl = "../pages/";
header("Location: $redirectUrl");
exit;