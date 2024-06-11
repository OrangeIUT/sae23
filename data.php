<?php

// Database login
include 'db_connect.php';

// Fetching the latest data for all sensors
$sql = "
    SELECT 
        c.nom_salle, 
        c.nom_capteur, 
        c.type, 
        m.valeur, 
        m.date 
    FROM 
        capteur c 
    JOIN 
        mesure m 
    ON 
        c.nom_capteur = m.nom_capteur 
    WHERE 
        m.date = (
            SELECT 
                MAX(date) 
            FROM 
                mesure 
            WHERE 
                nom_capteur = c.nom_capteur
        )
    ORDER BY 
        c.nom_salle, c.nom_capteur
";
$result = $conn->query($sql);

$rooms = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rooms[$row['nom_salle']][] = $row;
    }
} else {
    echo "0 results";
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <title>SAE23 - Données</title>
    <meta http-equiv="refresh" content="120"/>               <!-- Will refresh page every 2 minutes -->
    <link rel="stylesheet" type="text/css" href="styles/style.css"/> <!-- METTRE CSS ICI -->
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
    <div>
        <?php foreach ($rooms as $room => $sensors): ?>
            <h2>Bâtiment E</h2>
            <table>
                <tr>
                    <td colspan="2">
                        <?= $room ?> <!-- Gather room number -->
                    </td>
                </tr>
                <tr>
                    <?php foreach ($sensors as $sensor): ?>
                        <td><?= $sensor['type'] ?></td> <!-- Gather value type -->
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <?php foreach ($sensors as $sensor): ?>
                        <td><?= $sensor['valeur'] ?></td> <!-- Gather value -->
                    <?php endforeach; ?>
                </tr>
                <tr>
                    <td colspan="2">
                        <?= date('d/m/Y à H:i', strtotime($sensors[0]['date'])) ?> <!-- Gather date and convert it -->
                    </td>
                </tr>
            </table>
        <?php endforeach; ?>
    </div>
</main>
</body>
</html>