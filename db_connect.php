<?php
$servername = "your_server_name";
$username = "your_username";
$password = "your_password";
$dbname = "your_database_name";

// Create connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Verify connexion
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>