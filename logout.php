<?php
session_start();

// Destroy the session
$_SESSION = array();
session_destroy();

// Expire the cookies
if (isset($_COOKIE['loggedin'])) {
    setcookie('loggedin', '', time() - 3600, '/');
}
if (isset($_COOKIE['user_type'])) {
    setcookie('user_type', '', time() - 3600, '/');
}

// Detect the user type and redirect to the appropriate login page
if (isset($_COOKIE['user_type'])) {
    $user_type = $_COOKIE['user_type'];
    setcookie('user_type', '', time() - 3600, '/');  // Expire the cookie

    if ($user_type == 'gestionnaire') {
        header("Location: login_gestion.php");
    } elseif ($user_type == 'admin') {
        header("Location: login_admin.php");
    } else {
        header("Location: index.html"); // Default redirection
    }
} else {
    header("Location: index.html"); // Default redirection
}

exit;
?>
