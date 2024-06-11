<?php
session_start();

// Check if connexion is already active using cookies
if (isset($_COOKIE['loggedin']) && $_COOKIE['loggedin'] === 'true') {
    if ($_COOKIE['user_type'] === 'admin') {
        header("Location: administration.php");
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connect.php';

    $username = $_POST['login'];
    $password = $_POST['password'];

    // SQL injection protection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Check if the user is an admin
    $admin_sql = "SELECT * FROM administration WHERE id_admin = '$username' AND mdp_admin = '$password'";
    $admin_result = $conn->query($admin_sql);

    if ($admin_result->num_rows == 1) {
        // Correct admin login
        $row = $admin_result->fetch_assoc();
        $user_type = 'admin';

        // Set cookies for logged in status and user type
        setcookie('loggedin', 'true', time() + (86400 * 30), "/"); // 30 days
        setcookie('user_type', $user_type, time() + (86400 * 30), "/");

        // Redirection
        header("Location: administration.php");
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>SAE23 - Login admin</title>
    <meta http-equiv="refresh" content="120"/> <!-- Will refresh page every 2 minutes -->
    <link rel="stylesheet" type="text/css" href="styles/style.css"/>
</head>

<body>
<header>
    <nav>
        <ul>
            <li><a href="index.html">Accueil</a></li>
            <li><a href="data.php">Données</a></li>
            <li><a href="login_gestion.php">Gestion</a></li>
            <li><a href="login_admin.php">Administration</a></li>
            <li><a href="gantt.html">Gestion de projet</a></li>
            <li><a href="http://localhost:1880/ui">NodeRed</a></li>
            <li><a href="http://localhost:3000">Grafana</a></li>
            <li><a href="mentions.html">Mentions légales</a></li>
        </ul>
    </nav>
</header>
<main>
    <section>
        <h1>Connexion administrateur</h1>

        <form method="post" action="">
            <label for="login">Nom d'utilisateur :&nbsp;</label>
            <label>
                <input type="text" name="login" required>
            </label><br>
            <label for="password">Mot de passe :&nbsp;</label>
            <label>
                <input type="password" name="password" required>
            </label><br>
            <input type="submit" value="Se connecter">
        </form>
        <?php
        if (isset($error)) {
            echo "<p style='color:red;'>$error</p>";
        }
        ?>
    </section>
</main>
</body>
</html>



