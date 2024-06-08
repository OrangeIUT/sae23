<?php
session_start();
session_destroy();
header('Location: login_gestion.php'); //Will execute script whenever somethign is cliocked in header
exit;
?>