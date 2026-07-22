<?php
// Loads environment-aware DB credentials (see config.php), then opens the mysqli
// connection. Every page includes this file and uses the resulting $conn.
require_once __DIR__ . '/config.php';

// Create connection
$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

// Check connection
if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

// Set charset to utf8 for better character support
$conn->set_charset('utf8');
