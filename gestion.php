<?php
session_start();

// Connection verification
if (!isset($_COOKIE['loggedin']) || $_COOKIE['loggedin'] !== 'true' || !isset($_COOKIE['user_type']) || $_COOKIE['user_type'] !== 'gestionnaire') {
    header("Location: login_gestion.php");
    exit;
}

// Database login
include 'db_connect.php';

// Get building name from cookie
$building_name = isset($_COOKIE['building_name']) ? $_COOKIE['building_name'] : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>SAE23 - Gestion bâtiment</title>
    <meta http-equiv="refresh" content="120"/> <!-- Will refresh page every 2 minutes -->
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
        <h2>
            Bâtiment <?php echo $building_name; ?> <!-- Display building name from cookie -->
        </h2>

        <table>
            <tr>
                <th>Historique des valeurs:</th>
            </tr>
            <tr>
                <?php
                // Fetch the last 10 values from 'mesure' table for the specified building
                $sql = "
                SELECT mesure.date, mesure.valeur, capteur.unite, capteur.nom_salle
                FROM mesure 
                JOIN capteur ON mesure.nom_capteur = capteur.nom_capteur
                JOIN salle ON capteur.nom_salle = salle.nom_salle
                JOIN batiment ON salle.nom_bat = batiment.nom_bat
                WHERE batiment.nom_bat = '$building_name' AND capteur.active = 1
                ORDER BY mesure.date DESC 
                LIMIT 10";
                $result = $conn->query($sql);

                // Loop through the results and create table rows
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["date"] . "</td><td>" . $row["valeur"] . $row["unite"] . "</td><td>" . $row["capteur.nom_salle"] . "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No data found</td></tr>";
                }

                ?>
            </tr>
        </table>
    </section>
    <section>
        <table>
            <tr>
                <th><h2>Statistiques</h2></th>
            </tr>
            <tr>
                <td><h3>Minimum :</h3></td>
                <td><h3>Maximum :</h3></td>
                <td><h3>Moyenne :</h3></td>
            </tr>
            <tr>
                <?php
                // Fetch min, max and avg values for each type of sensor in the building
                $sql = "
                SELECT capteur.type, MIN(mesure.valeur) AS min, MAX(mesure.valeur) AS max, AVG(mesure.valeur) AS avg
                FROM mesure
                JOIN capteur ON mesure.nom_capteur = capteur.nom_capteur
                JOIN salle ON capteur.nom_salle = salle.nom_salle
                JOIN batiment ON salle.nom_bat = batiment.nom_bat
                WHERE batiment.nom_bat = '$building_name' AND capteur.active = 1
                GROUP BY capteur.type";
                $result = $conn->query($sql);

                // Loop through the results and create table rows
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["type"] . "</td><td>" . $row["min"] . "</td><td>" . $row["max"] . "</td><td>" . $row["avg"] . "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Pas de données</td></tr>";
                }

                ?>
            </tr>
        </table>
    </section>
</main>
</body>
</html>
