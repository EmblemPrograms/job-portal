<?php
// includes/admin-navbar.php
// This file is only used in the admin/ folder
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Security: Block direct access if not admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

include 'connect.php'; // Database connection

// Get admin full name
$admin_name = "Admin";
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT fullname, username FROM users WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $admin_name = $row['fullname'] ?: $row['username'];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - JobPortal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 260px;
            background: #343a40;
            color: white;
            padding-top: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .admin-sidebar .logo {
            text-align: center;
            padding: 20px 15px;
            font-size: 1.5rem;
            font-weight: bold;
            border-bottom: 1px solid #495057;
        }
        .admin-sidebar .nav-link {
            color: #cfd8dc;
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.3s;
        }
        .admin-sidebar .nav-link:hover,
        .admin-sidebar .nav-link.active {
            background: #495057;
            color: white;
        }
        .admin-sidebar .nav-link i {
            width: 25px;
            text-align: center;
        }
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }
        .topbar {
            background: white;
            padding: 15px 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        @media (max-width: 768px) {
            .admin-sidebar { width: 80px; }
            .admin-sidebar .logo, .admin-sidebar .nav-text { display: none; }
            .main-content { margin-left: 80px; }
        }
    </style>
</head>
<body>

<!-- Admin Sidebar -->
<div class="admin-sidebar">
    <div class="logo text-white">
        <i class="fas fa-cogs fa-2x"></i><br>Admin
    </div>
    <nav class="mt-4">
        <a href="dashboard.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i> <span class="nav-text">Dashboard</span>
        </a>
        <a href="add-job.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'add-job.php' ? 'active' : ''; ?>">
            <i class="fas fa-plus-circle"></i> <span class="nav-text">Add Job</span>
        </a>
        <a href="manage-jobs.php" class="nav-link <?php echo in_array(basename($_SERVER['PHP_SELF']), ['manage-jobs.php', 'edit-job.php', 'delete-job.php']) ? 'active' : ''; ?>">
            <i class="fas fa-briefcase"></i> <span class="nav-text">Manage Jobs</span>
        </a>
        <a href="manage-users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage-users.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> <span class="nav-text">Jobseekers</span>
        </a>
        <a href="applications.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'applications.php' ? 'active' : ''; ?>">
            <i class="fas fa-file-alt"></i> <span class="nav-text">Applications</span>
        </a>
    </nav>
</div>

<!-- Main Content Area -->
<div class="main-content">

    <!-- Top Bar -->
    <div class="topbar">
        <h4><i class="fas fa-user-shield text-primary"></i> Welcome, <strong><?php echo htmlspecialchars($admin_name); ?></strong></h4>
        <a href="../logout.php" class="btn btn-danger btn-sm">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <!-- Page Content Starts Here -->
    <div class="container-fluid">