<?php
$servername = "localhost";
$username = "tviallard";
$password = "Bonjour123";
$dbname = "sae23";

// Create connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Verify connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
