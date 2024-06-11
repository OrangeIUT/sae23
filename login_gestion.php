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
    </section>
</main>
</body>
</html>

<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'db_connect.php';

    $username = $_POST['login'];
    $password = $_POST['password'];

    // SQL injection protection
    $username = $conn->real_escape_string($username);
    $password = $conn->real_escape_string($password);

    // Identity verification through database
    $sql = "SELECT * FROM login WHERE nom_gest = '$username' AND mdp_gest = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Correct login
        $row = $result->fetch_assoc();
        $_SESSION['building_name'] = $row['nom_bat'];
        $_SESSION['loggedin'] = true;
        header("Location: gestion.php");
        exit;
    } else {
        $error = "Identifiants incorrects.";
    }

    $conn->close();
}
?>

