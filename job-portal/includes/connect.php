<?php
// Database configuration
$servername = "localhost";  // Usually localhost for XAMPP/WAMP
$username   = "root";       // Default XAMPP/WAMP username
$password   = "";           // Default password is empty
$dbname     = "job_portal"; // Change this if your database name is different

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set charset to utf8 for better character support
$conn->set_charset("utf8");

?>