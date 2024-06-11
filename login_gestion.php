

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>SAE23 connexion gestion</title>
    <meta http-equiv="refresh" content="120" /> <!-- Will refresh page every 2 minutes -->
    <link rel="stylesheet" type="text/css" href="styles/style.css" /> <!-- METTRE CSS ICI -->
</head>

<body>
    <header>
        <h3><a href="index.html">Retour</a></h3> <!-- Retourner à la page d'accueil -->
    </header>
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