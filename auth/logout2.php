<?php
session_start(); // Démarrer la session

// Détruire toutes les données de session
session_unset();

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
header("Location: login_admin.php");
exit();
?>
