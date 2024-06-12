<?php
// Database login
include 'db_connect.php';

$sql = "
SELECT batiment.nom_bat, salle.nom_salle, capteur.nom_capteur, mesure.date, mesure.valeur, capteur.unite
FROM capteur
JOIN salle ON capteur.nom_salle = salle.nom_salle
JOIN batiment ON salle.nom_bat = batiment.nom_bat
JOIN (
    SELECT nom_capteur, MAX(date) as max_date 
    FROM mesure
    GROUP BY nom_capteur
) latest ON c.nom_capteur = latest.nom_capteur
JOIN mesure ON latest.nom_capteur = mesure.nom_capteur AND latest.max_date = mesure.date
WHERE capteur.active = 1
ORDER BY batiment.nom_bat, salle.nom_salle, capteur.nom_capteur";

$result = $conn->query($sql);

// Check if the query was successful before attempting to fetch results
if ($result) {
    if ($result->num_rows > 0) {
        $sensors_data = [];
        while ($row = $result->fetch_assoc()) {
            $sensors_data[$row['nom_bat']][$row['nom_salle']][] = $row;
        }
    } else {
        $sensors_data = [];
    }
} else {
    // Handle the case where the query fails
    echo "Error executing SQL query: " . $conn->error;
    // You might want to log this error for further investigation
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dernières valeurs des capteurs</title>
    <link rel="stylesheet" type="text/css" href="styles/style.css"/> <!-- Link to CSS file -->
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
        <h2>Dernières valeurs des capteurs</h2>
        <table>
            <tr>
                <th>Bâtiment</th>
                <th>Salle</th>
                <th>Date</th>
                <th>Valeur</th>
            </tr>

            <?php
            foreach ($sensors_data as $building => $rooms) {
                foreach ($rooms as $room => $sensors) {
                    foreach ($sensors as $sensor) {
                        echo "<tr>";
                        echo "<td>" . $building . "</td>";
                        echo "<td>" . $room . "</td>";
                        echo "<td>" . $sensor['date'] . "</td>";
                        echo "<td>" . $sensor['valeur'] . " " . $sensor['unite'] . "</td>";
                        echo "</tr>";
                    }
                }
            }
            ?>
        </table>
    </section>
</main>
</body>
</html>
