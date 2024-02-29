<?php
session_start();

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion (login.php)
header("Location: login.php");
exit;
?>
