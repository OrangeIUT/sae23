<?php
// Database login
include 'db_connect.php';

$sql = "
SELECT b.nom_bat, s.nom_salle, c.nom_capteur, m.date, m.valeur, c.unite
FROM capteur c
JOIN salle s ON c.nom_salle = s.nom_salle
JOIN batiment b ON s.nom_bat = b.nom_bat
JOIN (
    SELECT nom_capteur, MAX(date) as max_date 
    FROM mesure 
    GROUP BY nom_capteur
) latest ON c.nom_capteur = latest.nom_capteur
JOIN mesure m ON latest.nom_capteur = m.nom_capteur AND latest.max_date = m.date
WHERE c.active = 1
ORDER BY b.nom_bat, s.nom_salle, c.nom_capteur";

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
        <?php if (!empty($sensors_data)): ?>
            <?php foreach ($sensors_data as $batiment => $salles): ?>
                <h3>Bâtiment: </h3> <!-- batiment request -->
                <?php foreach ($salles as $salle => $capteurs): ?>
                    <h4>Salle: </h4> <!-- salle request -->
                    <table>
                        <tr>
                            <th>Capteur</th>
                            <th>Date</th>
                            <th>Valeur</th>
                            <th>Unité</th>
                        </tr>
                            <tr>
                                <td></td>
                                <td><</td> <!-- Requests here for latest values -->
                                <td></td>
                                <td></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Aucune donnée disponible</p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
