<?php
session_start();

// Connexion verification
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login_gestion.php");
    exit;
}

// Database login
include 'db_connect.php';
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
    <div>
        <h2><?php echo $_SESSION['building_name']; ?></h2> <!-- Display building name from session -->
        <table>
            <tr>
                <th>Historique des valeurs:</th>
            </tr>
            <tr>
                <?php
                // Include the database connection script
                include 'db_connect.php';

                // Fetch the last 10 values from 'mesure' table
                $sql = "SELECT date, valeur, nom_capteur FROM mesure ORDER BY date DESC LIMIT 10";
                $result = $conn->query($sql);

                // Loop through the results and create table rows
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["date"] . "</td><td>" . $row["valeur"] . "</td><td>" . $row["nom_capteur"] . "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No data found</td></tr>";
                }

                // Close the connection
                $conn->close();
                ?>
            </tr>
        </table>
        <table>
            <tr>
                <th><h2>Statistiques</h2></th>
            </tr>
            <tr>
                <td><h3>Minimum:</h3></td>
                <td><h3>Maximum:</h3></td>
                <td><h3>Moyenne:</h3></td>
            </tr>
            <tr>
                <?php
                // Include the database connection script
                include 'db_connect.php';

                // Fetch the statistics
                $sql_min = "SELECT MIN(valeur) AS min_val FROM mesure";
                $sql_max = "SELECT MAX(valeur) AS max_val FROM mesure";
                $sql_avg = "SELECT AVG(valeur) AS avg_val FROM mesure";

                $min_result = $conn->query($sql_min);
                $max_result = $conn->query($sql_max);
                $avg_result = $conn->query($sql_avg);

                $min_val = $min_result->fetch_assoc()['min_val'];
                $max_val = $max_result->fetch_assoc()['max_val'];
                $avg_val = $avg_result->fetch_assoc()['avg_val'];

                echo "<td><h3>$min_val</h3></td>";
                echo "<td><h3>$max_val</h3></td>";
                echo "<td><h3>$avg_val</h3></td>";

                // Close the connection
                $conn->close();
                ?>
            </tr>
        </table>
    </div>
</main>
</body>
</html>
