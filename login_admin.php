<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>SAE23 - Login admin</title>
    <meta http-equiv="refresh" content="120"/> <!-- Will refresh page every 2 minutes -->
    <link rel="stylesheet" type="text/css" href="styles/style.css"/> <!-- METTRE CSS ICI -->
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
            <li><a href="http://localhost:1880/ui">Grafana</a></li>
            <li><a href="mentions.html">Mentions légales</a></li>
        </ul>
    </nav>
</header>
<main>
    <h1>Connexion administrateur</h1>
    <div>
        <form method="post" action="">
            <label for="login">Login:&nbsp;</label>
            <label>
                <input type="text" name="login" required>
            </label><br>
            <label for="password">Password:&nbsp;</label>
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
    </div>
</main>
</body>
</html>

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connect.php';

    $username = $_POST['login'];
    $password = $_POST['password'];

    // Protection against SQL injection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Verify login with DB
    $sql = "SELECT * FROM administration WHERE id_admin = '$username' AND mdp_admin = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Correct login
        $_SESSION['admin_loggedin'] = true;
        header("Location: administration.php");
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }

    $conn->close();
}
?>

