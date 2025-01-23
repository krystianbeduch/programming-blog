<?php
session_start();
// Usuniecie zmiennej sesyjnej
if (isset($_SESSION["loggedUser"])) {
    unset($_SESSION["loggedUser"]);
}

if (isset($_SESSION["formData"])) {
    unset($_SESSION["formData"]);
}

$_SESSION["alert"]["successStrong"] = "";
$_SESSION["alert"]["success"] = "Wylogowano pomyślnie";

// Przekierowanie na strone z ktorej nastapilo wylogowanie
$redirectUrl = "../pages/";
header("Location: $redirectUrl");
exit();