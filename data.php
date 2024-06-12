<?php
// Database login
include 'db_connect.php';

// Get the last value from each active sensor
$sql = "
SELECT batiment.nom_bat, salle.nom_salle, mesure.date, capteur.type, mesure.valeur, capteur.unite
FROM mesure
JOIN capteur ON mesure.nom_capteur = capteur.nom_capteur
JOIN salle ON capteur.nom_salle = salle.nom_salle
JOIN batiment ON salle.nom_bat = batiment.nom_bat
WHERE capteur.active = 1
GROUP BY capteur.nom_capteur
ORDER BY mesure.date DESC
LIMIT 1";
$result = $conn->query($sql);

// Loop through the results and create table rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Dictionary with types from database linked to 'showable' types
        $types = array(
            "temperature" => "Température",
            "humidity" => "Humidité",
            "co2" => "CO2",
            "tvoc" => "TVOC",
            "illumination" => "Luminosité",
            "pressure" => "Pression",
        );
        $to_add = "<tr><td>" . $row["nom_bat"] . "</td><td>" . $row["nom_salle"] . "</td><td>" . $types[$row["type"]] . "</td><td>" . $row["date"] . "</td><td>" . $row["valeur"] . $row["unite"] . "</td></tr>";
    }
} else {
    $to_add = "<tr><td colspan='5'>No data found</td></tr>";
}
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
                <th>Type</th>
                <th>Date</th>
                <th>Valeur</th>
            </tr>

            <?php echo $to_add; ?>
        </table>
    </section>
</main>
</body>
</html>
