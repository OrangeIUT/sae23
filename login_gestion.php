<?php
session_start();

// Check if connexion is already active using cookies
if (isset($_COOKIE['loggedin']) && $_COOKIE['loggedin'] === 'true' && isset($_COOKIE['user_type']) && $_COOKIE['user_type'] === 'gestionnaire'){
    header("Location: gestion.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connect.php';

    $username = $_POST['login'];
    $password = $_POST['password'];

    // SQL injection protection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Check if the user is a manager
    $gest_sql = "SELECT * FROM login WHERE nom_gest = '$username' AND mdp_gest = '$password'";
    $gest_result = $conn->query($gest_sql);

    if ($gest_result->num_rows == 1) {
        // Correct manager login
        $row = $gest_result->fetch_assoc();
        $user_type = 'gestionnaire';
        // Building name: in 'batiment' table, get the name of the building where the manager works
        $building_sql = "SELECT nom_bat FROM batiment WHERE nom_gest = '$username'";
        $building_result = $conn->query($building_sql);

        // Set cookies for logged in status, user type and building name
        setcookie('loggedin', 'true', time() + (86400 * 30), "/"); // 30 days
        setcookie('user_type', $user_type, time() + (86400 * 30), "/");
        setcookie('building_name', $building_result, time() + (86400 * 30), "/");

        // Redirection
        header("Location: gestion.php");
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
    <title>SAE23 - Login gestion</title>
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
        <h1>Connexion gestionnaire</h1>


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
