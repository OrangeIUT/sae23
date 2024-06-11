<?php
session_start();
session_destroy();
header('Location: gestion.php'); //Will execute script whenever something is cliocked in header
exit;
?>

<?php
session_start();
session_destroy();
header('Location: administration.php'); //Will execute script whenever something is cliocked in header
exit;
?>

