<?php session_start();
session_unset();
session_destroy();
header("Location:portail_connexion.php");
?>
