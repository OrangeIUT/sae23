<?php
session_start();

// Connexion verification
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
    header("Location: login_admin.php");
    exit;
}

// Database login
include 'db_connect.php';

// Form processing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_batiment'])) {                                         //Process for building
        $nom_bat = $_POST['nom_bat'];
        $nom_gest = $_POST['nom_gest'];
        $sql = "INSERT INTO batiment (nom_bat, nom_gest) VALUES ('$nom_bat', '$nom_gest')";
        $conn->query($sql);
    } elseif (isset($_POST['delete_batiment'])) {
        $nom_bat = $_POST['nom_bat'];
        $sql = "DELETE FROM batiment WHERE nom_bat = '$nom_bat'";
        $conn->query($sql);
    } elseif (isset($_POST['create_salle'])) {                                         //Process for room
        $nom_salle = $_POST['nom_salle'];
        $type = $_POST['type'];
        $capacite = $_POST['capacite'];
        $nom_bat = $_POST['nom_bat'];
        $sql = "INSERT INTO salle (nom_salle, type, capacite, nom_bat) VALUES ('$nom_salle', '$type', '$capacite', '$nom_bat')";
        $conn->query($sql);
    } elseif (isset($_POST['delete_salle'])) {
        $nom_salle = $_POST['nom_salle'];
        $sql = "DELETE FROM salle WHERE nom_salle = '$nom_salle'";
        $conn->query($sql);
    } elseif (isset($_POST['create_capteur'])) {                                         //Process for sensor
        $nom_capteur = $_POST['nom_capteur'];
        $type = $_POST['type'];
        $unite = $_POST['unite'];
        $nom_salle = $_POST['nom_salle'];
        $sql = "INSERT INTO capteur (nom_capteur, type, unite, nom_salle) VALUES ('$nom_capteur', '$type', '$unite', '$nom_salle')";
        $conn->query($sql);
    } elseif (isset($_POST['delete_capteur'])) {
        $nom_capteur = $_POST['nom_capteur'];
        $sql = "DELETE FROM capteur WHERE nom_capteur = '$nom_capteur'";
        $conn->query($sql);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>SAE23 gestion Administration</title>
    <meta http-equiv="refresh" content="120" /> <!-- Will refresh page every 2 minutes -->
    <link rel="stylesheet" type="text/css" href="PAGE CSS" /> <!-- Link to CSS file -->
</head>

<body>
    <header>
        <table>
            <tr>
                <td><h3><a href="logout.php">Déconnexion</a></h3></td><!-- Destroy session cookie -->
                <td><h3><a href="index.html">Retour</a></h3></td>
            </tr>
        </table>
    </header>
    <div>
        <h2>Gestion des Bâtiments</h2>                                           <!-- Forms for building -->
        <form method="post">
            <label for="nom_bat">Nom du bâtiment:</label>
            <input type="text" name="nom_bat" required>
            <label for="nom_gest">Nom du gestionnaire:</label>
            <input type="text" name="nom_gest" required>
            <input type="submit" name="create_batiment" value="Créer Bâtiment">
        </form>
        <form method="post">
            <label for="nom_bat">Nom du bâtiment à supprimer:</label>
            <input type="text" name="nom_bat" required>
            <input type="submit" name="delete_batiment" value="Supprimer Bâtiment">
        </form>

        <h2>Gestion des Salles</h2>                                           <!-- Forms for rooms -->
        <form method="post">
            <label for="nom_salle">Nom de la salle:</label>
            <input type="text" name="nom_salle" required>
            <label for="type">Type:</label>
            <input type="text" name="type" required>
            <label for="capacite">Capacité:</label>
            <input type="number" name="capacite" required>
            <label for="nom_bat">Nom du bâtiment:</label>
            <input type="text" name="nom_bat" required>
            <input type="submit" name="create_salle" value="Créer Salle">
        </form>
        <form method="post">
            <label for="nom_salle">Nom de la salle à supprimer:</label>
            <input type="text" name="nom_salle" required>
            <input type="submit" name="delete_salle" value="Supprimer Salle">
        </form>

        <h2>Gestion des Capteurs</h2>                                           <!-- Forms for sensors -->
        <form method="post">
            <label for="nom_capteur">Nom du capteur:</label>
            <input type="text" name="nom_capteur" required>
            <label for="type">Type:</label>
            <input type="text" name="type" required>
            <label for="unite">Unité:</label>
            <input type="text" name="unite" required>
            <label for="nom_salle">Nom de la salle:</label>
            <input type="text" name="nom_salle" required>
            <input type="submit" name="create_capteur" value="Créer Capteur">
        </form>
        <form method="post">
            <label for="nom_capteur">Nom du capteur à supprimer:</label>
            <input type="text" name="nom_capteur" required>
            <input type="submit" name="delete_capteur" value="Supprimer Capteur">
        </form>
    </div>
</body>
</html>