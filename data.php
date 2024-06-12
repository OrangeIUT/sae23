<?php
// Database login
include 'db_connect.php';

// Get the value from each active sensor
// use latest
$sql = "
SELECT batiment.nom_bat, salle.nom_salle, capteur.type, mesure.date, mesure.valeur, capteur.unite
FROM mesure
JOIN capteur ON mesure.nom_capteur = capteur.nom_capteur
JOIN salle ON capteur.nom_salle = salle.nom_salle
JOIN batiment ON salle.nom_bat = batiment.nom_bat
JOIN (
    SELECT nom_capteur, MAX(date) AS max_date
    FROM mesure
    GROUP BY nom_capteur
) AS latest
ON mesure.nom_capteur = latest.nom_capteur AND mesure.date = latest.max_date
WHERE capteur.active = 1
";

$result = $conn->query($sql);

$to_add = "";
$types = array(
    "temperature" => "Température",
    "humidity" => "Humidité",
    "co2" => "CO&#8322;",
    "tvoc" => "TVOC",
    "illumination" => "Luminosité",
    "pressure" => "Pression",
);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $to_add .= "<tr><td>" . $row["nom_bat"] . "</td><td>" . $row["nom_salle"] . "</td><td>" . $row["type"] . "</td><td>" . $row["date"] . "</td><td>" . $row["valeur"] . $row["unite"] . "</td></tr>";
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
