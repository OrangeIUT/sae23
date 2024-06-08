<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>SAE23_données Administration</title>
    <meta http-equiv="refresh" content="120" /> <!-- Will refresh page every 2 minutes -->
    <link rel="stylesheet" type="text/css" href="PAGE CSS" /> <!-- METTRE CSS ICI -->
</head>

<body>
    <header>
        <h3><a href="index.html">Retour</a></h3>
    </header>
    <div>
        <form method="post" action="">
            <label for="login">Login:&nbsp;</label>
            <input type="text" name="login" required><br>
            <label for="password">Password:&nbsp;</label>
            <input type="password" name="password" required><br>
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
