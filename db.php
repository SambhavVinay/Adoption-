<?php
// Database connection parameters
$host = "localhost";           // Server (localhost for local server)
$user = "root";                // Username (default in XAMPP)
$password = "";                // No password by default
$database = "pet_adoption";    // Name of your database

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check if connection is successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // Error message if connection fails
}
?>
