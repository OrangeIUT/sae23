<?php
session_start();

// Connection verification
if (!isset($_COOKIE['loggedin']) || $_COOKIE['loggedin'] !== 'true' || !isset($_COOKIE['user_type']) || $_COOKIE['user_type'] !== 'admin') {
    header("Location: login_admin.php");
    exit;
}

// Database login
include 'db_connect.php';

// Form processing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['create_batiment'])) { // Process for building
        $nom_bat = $_POST['nom_bat'];
        $nom_gest = $_POST['nom_gest'];
        $sql = "INSERT INTO batiment (nom_bat, nom_gest) VALUES ('$nom_bat', '$nom_gest')";
        $conn->query($sql);
    } elseif (isset($_POST['delete_batiment'])) {
        $nom_bat = $_POST['nom_bat'];
        $sql = "DELETE FROM batiment WHERE nom_bat = '$nom_bat'";
        $conn->query($sql);
    } elseif (isset($_POST['create_salle'])) { // Process for room
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
    } elseif (isset($_POST['create_capteur'])) { // Process for sensor
        $nom_capteur = $_POST['nom_salle'] . '_' . $_POST['type'];
        $type = $_POST['type'];
        // Set the unit based on the sensor type
        switch ($type) {
            case 'temperature':
                $unite = '&deg;C';
                break;
            case 'humidity':
                $unite = '%';
                break;
            case 'co2':
                $unite = 'ppm';
                break;
            case 'tvoc':
                $unite = 'ppb';
                break;
            case 'illumination':
                $unite = 'lux';
                break;
            case 'pressure':
                $unite = 'Pa';
                break;
            default:
                $unite = '';
        }
        $nom_salle = $_POST['nom_salle'];
        $sql = "INSERT INTO capteur (nom_capteur, type, unite, nom_salle) VALUES ('$nom_capteur', '$type', '$unite', '$nom_salle')";
        $conn->query($sql);
    } elseif (isset($_POST['delete_capteur'])) {
        $nom_capteur = $_POST['nom_capteur'];
        $sql = "DELETE FROM capteur WHERE nom_capteur = '$nom_capteur'";
        $conn->query($sql);
    } elseif (isset($_POST['create_gest'])) { // Process for adding a manager
        $nom_gest = $_POST['nom_gest'];
        $mdp_gest = $_POST['mdp_gest'];

        // Check if the manager name and password are not empty
        if (!empty($nom_gest) && !empty($mdp_gest)) {
            $sql = "INSERT INTO login (nom_gest, mdp_gest) VALUES ('$nom_gest', '$mdp_gest')";
            $conn->query($sql);
        } else {
            echo "Le nom du gestionnaire et le mot de passe sont requis.";
        }

    } elseif (isset($_POST['delete_gest'])) { // Process for deleting a manager
        $nom_gest = $_POST['nom_gest'];
        $sql = "DELETE FROM login WHERE nom_gest = '$nom_gest'";
        $conn->query($sql);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>SAE23 - Administration</title>
    <link rel="stylesheet" type="text/css" href="styles/style.css"/> <!-- Link to CSS file -->
</head>

<body>
<header>
    <nav>
        <ul>
            <li><a href="index.html">Accueil</a></li>
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>
<main>
    <section>
        <h2>Gestion des bâtiments</h2>
        <!-- Forms for building -->
        <form method="post">
            <h3>Créer un bâtiment</h3>
            <label for="nom_bat">Nom du bâtiment :</label>
            <label>
                <input type="text" name="nom_bat" maxlength="1" required>
            </label>
            <label for="nom_gest">Nom du gestionnaire :</label>
            <label>
                <select name="nom_gest" required>
                    <?php
                    // Fetching existing managers
                    $sql_managers = "SELECT nom_gest FROM login";
                    $result_managers = $conn->query($sql_managers);

                    if ($result_managers->num_rows > 0) {
                        while ($row = $result_managers->fetch_assoc()) {
                            echo "<option value='" . $row['nom_gest'] . "'>" . $row['nom_gest'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </label>
            <input type="submit" name="create_batiment" value="Créer bâtiment">
        </form>
        <form method="post">
            <h3>Supprimer un bâtiment</h3>
            <label for="nom_bat">Nom du bâtiment à supprimer :</label>
            <label>
                <select name="nom_bat" required>
                    <?php
                    // Fetching existing buildings
                    $sql_buildings = "SELECT nom_bat FROM batiment";
                    $result_buildings = $conn->query($sql_buildings);

                    if ($result_buildings->num_rows > 0) {
                        while ($row = $result_buildings->fetch_assoc()) {
                            echo "<option value='" . $row['nom_bat'] . "'>" . $row['nom_bat'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </label>
            <input type="submit" name="delete_batiment" value="Supprimer bâtiment">
        </form>
    </section>
    <section>
        <h2>Gestion des salles</h2>
        <!-- Forms for rooms -->
        <form method="post">
            <h3>Créer une salle</h3>
            <label for="nom_bat">Nom du bâtiment :</label>
            <label>
                <select name="nom_bat" required>
                    <?php
                    // Fetching existing buildings
                    $sql_buildings = "SELECT nom_bat FROM batiment";
                    $result_buildings = $conn->query($sql_buildings);

                    if ($result_buildings->num_rows > 0) {
                        while ($row = $result_buildings->fetch_assoc()) {
                            echo "<option value='" . $row['nom_bat'] . "'>" . $row['nom_bat'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </label>
            <label for="nom_salle">Nom de la salle :</label>
            <label>
                <input type="text" name="nom_salle" maxlength="35" required>
            </label>
            <label for="type">Type (CM, TD, TP ou NA) :</label>
            <label>
                <select name="type" required>
                    <option value="CM">CM</option>
                    <option value="TD">TD</option>
                    <option value="TP">TP</option>
                    <option value="NA">Non défini</option>
                </select>
            </label>
            <label for="capacite">Capacité :</label>
            <label>
                <input type="number" name="capacite" required>
            </label>

            <input type="submit" name="create_salle" value="Créer salle">
        </form>
        <form method="post">
            <label for="nom_salle">Nom de la salle à supprimer :</label>
            <label>
                <select name="nom_salle" required>
                    <?php
                    // Fetching existing rooms
                    $sql_rooms = "SELECT nom_salle FROM salle";
                    $result_rooms = $conn->query($sql_rooms);

                    if ($result_rooms->num_rows > 0) {
                        while ($row = $result_rooms->fetch_assoc()) {
                            echo "<option value='" . $row['nom_salle'] . "'>" . $row['nom_salle'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </label>
            <input type="submit" name="delete_salle" value="Supprimer salle">
        </form>
    </section>
    <section>
        <h2>Gestion des capteurs</h2>
        <!-- Forms for sensors -->
        <form method="post">
            <h3>Créer un capteur</h3>
            <label for="nom_salle">Nom de la salle :</label>
            <label>
                <select name="nom_salle" required>
                    <?php
                    // Fetching existing rooms
                    $sql_rooms = "SELECT nom_salle FROM salle";
                    $result_rooms = $conn->query($sql_rooms);

                    if ($result_rooms->num_rows > 0) {
                        while ($row = $result_rooms->fetch_assoc()) {
                            echo "<option value='" . $row['nom_salle'] . "'>" . $row['nom_salle'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </label>
            <label for="type">Type :</label>
            <label>
                <select name="type" required>
                    <option value="temperature">Température</option>
                    <option value="humidity">Humidité</option>
                    <option value="co2">CO2</option>
                    <option value="tvoc">TVOC</option>
                    <option value="illumination">Luminosité</option>
                    <option value="pressure">Pression</option>
                </select>
            </label>

            <input type="submit" name="create_capteur" value="Créer capteur">
        </form>
        <form method="post">
            <h3>Supprimer un capteur</h3>
            <label for="nom_capteur">Nom du capteur à supprimer :</label>
            <label>
                <select name="nom_capteur" required>
                    <?php
                    // Fetching existing sensors
                    $sql_sensors = "SELECT nom_capteur FROM capteur";
                    $result_sensors = $conn->query($sql_sensors);

                    if ($result_sensors->num_rows > 0) {
                        while ($row = $result_sensors->fetch_assoc()) {
                            echo "<option value='" . $row['nom_capteur'] . "'>" . $row['nom_capteur'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </label>
            <input type="submit" name="delete_capteur" value="Supprimer capteur">
        </form>
    </section>
    <section>
        <h2>Gestion des gestionnaires</h2>
        <!-- Forms for managers -->
        <form method="post">
            <h3>Ajouter un gestionnaire</h3>
            <label for="nom_gest">Nom du gestionnaire :</label>
            <label>
                <input type="text" name="nom_gest" required>
            </label>
            <label for="mdp_gest">Mot de passe :</label>
            <label>
                <input type="password" name="mdp_gest" required>
            </label>
            <input type="submit" name="create_gest" value="Ajouter gestionnaire">
        </form>
        <form method="post">
            <label for="nom_gest">Nom du gestionnaire à supprimer :</label>
            <label>
                <select name="nom_gest" required>
                    <?php
                    // Fetching existing managers
                    $sql_managers = "SELECT nom_gest FROM login";
                    $result_managers = $conn->query($sql_managers);

                    if ($result_managers->num_rows > 0) {
                        while ($row = $result_managers->fetch_assoc()) {
                            echo "<option value='" . $row['nom_gest'] . "'>" . $row['nom_gest'] . "</option>";
                        }
                    }
                    ?>
                </select>
            </label>
            <input type="submit" name="delete_gest" value="Supprimer gestionnaire">
        </form>
    </section>
</main>
</body>
</html>