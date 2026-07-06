<?php
session_start();
include '../includes/admin-navbar.php'; // must define $conn (mysqli connection)

// Enable error reporting during development (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
//     $_SESSION['error'] = "Unauthorized access.";
//     header("Location: ../login.php");
//     exit;
// }

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['error'] = "Invalid request method.";
    header("Location: applications.php");
    exit;
}

// Sanitize and validate input
$app_id = isset($_POST['app_id']) ? (int)$_POST['app_id'] : 0;
$action  = $_POST['action'] ?? '';

if ($app_id <= 0 || !in_array($action, ['approve', 'reject'])) {
    $_SESSION['error'] = "Invalid application ID or action.";
    header("Location: applications.php");
    exit;
}

$new_status = ($action === 'approve') ? 'approved' : 'rejected';

$stmt = $conn->prepare("UPDATE applications SET status = ? WHERE id = ?");
if (!$stmt) {
    $_SESSION['error'] = "Database prepare error: " . $conn->error;
    header("Location: applications.php");
    exit;
}

$stmt->bind_param("si", $new_status, $app_id);

if ($stmt->execute()) {
    // Optional: Add email notification here later
    // Example: sendApplicationStatusEmail($app_id, $new_status);

    $_SESSION['success'] = "Application #" . $app_id . " has been " . $new_status . " successfully.";
} else {
    $_SESSION['error'] = "Failed to update application: " . $stmt->error;
}

$stmt->close();

// Optional: close connection if config.php doesn't handle it
// $conn->close();

header("Location: applications.php");
exit;