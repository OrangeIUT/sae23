<?php
// Database login
include 'db_connect.php';

// Fetch the active sensors and their last measured values, ordered by building and room
$sql = "
SELECT b.nom_bat, s.nom_salle, c.nom_capteur, m.date, m.valeur, c.unite_mesure 
FROM capteur c
JOIN salle s ON c.nom_salle = s.nom_salle
JOIN batiment b ON s.nom_bat = b.nom_bat
JOIN (
    SELECT nom_capteur, MAX(date) as max_date 
    FROM mesure 
    GROUP BY nom_capteur
) latest ON c.nom_capteur = latest.nom_capteur
JOIN mesure m ON latest.nom_capteur = m.nom_capteur AND latest.max_date = m.date
WHERE c.actif = 1
ORDER BY b.nom_bat, s.nom_salle, c.nom_capteur";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $sensors_data = [];
    while ($row = $result->fetch_assoc()) {
        $sensors_data[$row['nom_bat']][$row['nom_salle']][] = $row;
    }
} else {
    $sensors_data = [];
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
            <li><a href="logout.php">Déconnexion</a></li>
        </ul>
    </nav>
</header>
<main>
    <section>
        <h2>Dernières valeurs des capteurs</h2>
        <?php if (!empty($sensors_data)): ?>
            <?php foreach ($sensors_data as $batiment => $salles): ?>
                <h3>Bâtiment: <?php echo $batiment; ?></h3>
                <?php foreach ($salles as $salle => $capteurs): ?>
                    <h4>Salle: <?php echo $salle; ?></h4>
                    <table>
                        <tr>
                            <th>Capteur</th>
                            <th>Date</th>
                            <th>Valeur</th>
                            <th>Unité</th>
                        </tr>
                        <?php foreach ($capteurs as $sensor): ?>
                            <tr>
                                <td><?php echo $sensor['nom_capteur']; ?></td>
                                <td><?php echo $sensor['date']; ?></td>
                                <td><?php echo $sensor['valeur']; ?></td>
                                <td><?php echo $sensor['unite_mesure']; ?></td>
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
