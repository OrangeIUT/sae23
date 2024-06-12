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
$building_name = $_COOKIE['building_name'];

$types = array(
    "temperature" => "Température",
    "humidity" => "Humidité",
    "co2" => "CO&#8322;",
    "tvoc" => "TVOC",
    "illumination" => "Luminosité",
    "pressure" => "Pression",
);

// Get last 10 values
$sql = "
SELECT capteur.type, mesure.date, mesure.valeur, capteur.unite, salle.nom_salle
FROM mesure
JOIN capteur ON mesure.nom_capteur = capteur.nom_capteur
JOIN salle ON capteur.nom_salle = salle.nom_salle
JOIN batiment ON salle.nom_bat = batiment.nom_bat
WHERE batiment.nom_bat = '$building_name'
ORDER BY mesure.date DESC
LIMIT 10;
";

$result = $conn->query($sql);

$history = "";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $history .= "<tr><td>" . $row["date"] . "</td><td>" . $types[$row["type"]] . "</td><td>" . $row["valeur"] . $row["unite"] . "</td><td>" . $row["nom_salle"] . "</td></tr>";
    }
} else {
    $history = "<tr><td colspan='4'>Aucune donnée trouvée</td></tr>";
}

// Get statistics
$stats = "";
foreach ($types as $type => $type_name) {
    $sql = "
    SELECT MIN(mesure.valeur) AS min_val, MAX(mesure.valeur) AS max_val, AVG(mesure.valeur) AS avg_val
    FROM mesure
    JOIN capteur ON mesure.nom_capteur = capteur.nom_capteur
    JOIN salle ON capteur.nom_salle = salle.nom_salle
    JOIN batiment ON salle.nom_bat = batiment.nom_bat
    WHERE capteur.type = '$type' AND batiment.nom_bat = '$building_name';
    ";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stats .= "<tr><td>" . $type_name . "</td><td>" . $row["min_val"] . "</td><td>" . $row["max_val"] . "</td><td>" . $row["avg_val"] . "</td></tr>";
    } else {
        $stats .= "<tr><td>" . $type_name . "</td><td colspan='3'>Aucune donnée trouvée</td></tr>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>SAE23 - Bâtiment <?php echo $building_name; ?></title>
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
                <th colspan="4"><h2>Historique</h2></th>
            </tr>
            <tr>
                <th>Date</th>
                <th>Type de capteur</th>
                <th>Valeur</th>
                <th>Salle</th>
            </tr>
            <?php echo $history; ?>
        </table>
    </section>
    <section>
        <table>
            <tr>
                <th colspan="4"><h2>Statistiques</h2></th>
            </tr>
            <tr>
                <td><h3>Type de capteur</h3></td>
                <td><h3>Minimum</h3></td>
                <td><h3>Maximum</h3></td>
                <td><h3>Moyenne</h3></td>
            </tr>
            <?php echo $stats; ?>
        </table>
    </section>
</main>
</body>
</html>
