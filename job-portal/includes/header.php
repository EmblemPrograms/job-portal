<?php
// includes/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'connect.php'; // Database connection

// Default values
$logged_in = false;
$username  = '';
$fullname  = '';
$is_admin  = false;

if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Verify session is valid (security)
    $stmt = $conn->prepare("SELECT username, fullname, role FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $logged_in = true;
        $username  = $user['username'];
        $fullname  = $user['fullname'] ?: $user['username'];
        $is_admin  = ($user['role'] === 'admin');
    } else {
        // Invalid session → logout
        session_destroy();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobPortal - Find Your Dream Job</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .navbar-brand { font-weight: bold; }
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .user-welcome { font-size: 0.95rem; }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-briefcase mr-2"></i>JobPortal
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="jobs.php">Browse Jobs</a></li>
            </ul>

            <ul class="navbar-nav ml-auto">
                <?php if ($logged_in): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle user-welcome text-white" href="#" role="button" data-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> Welcome, <?php echo htmlspecialchars($fullname); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="profile.php"><i class="fas fa-user"></i> My Profile</a>
                            <a class="dropdown-item" href="applications.php"><i class="fas fa-file-alt"></i> My Applications</a>
                            <div class="dropdown-divider"></div>
                            <?php if ($is_admin): ?>
                                <a class="dropdown-item text-danger" href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin Panel</a>
                                <div class="dropdown-divider"></div>
                            <?php endif; ?>
                            <a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link btn btn-light text-primary ml-2 px-3" href="register.php">Register</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>