<?php
// Database configuration - INSECURE: credentials hardcoded, using root
$host = 'localhost';
$dbname = 'inventory_db';
$username = 'root';
$password = '';

$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    // INSECURE: Exposing connection error details to user
    die("Connection failed: " . mysqli_connect_error());
}
?>
