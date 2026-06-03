<?php
session_start();
session_destroy();
header('Location: intranet_login.php');
exit();
?>
