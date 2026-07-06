<?php


if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'connect.php'; // Database connection

// Default values
$logged_in = false;
$fullname  = 'User';
$username  = '';
$is_admin  = false;

if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT fullname, username, role FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $logged_in = true;
        $username  = $user['username'];
        $fullname  = !empty($user['fullname']) ? $user['fullname'] : $user['username'];
        $is_admin  = ($user['role'] === 'admin');
    } else {
        // Invalid session → force logout
        session_destroy();
        header("Location: login.php");
        exit();
    }
    $stmt->close();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <!-- Brand -->
        <a class="navbar-brand font-weight-bold" href="index.php">
            <i class="fas fa-briefcase mr-2"></i>JobPortal
        </a>

        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Left Links -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                        <i class="fas fa-home"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'jobs.php' ? 'active' : ''; ?>" href="jobs.php">
                        <i class="fas fa-search"></i> Browse Jobs
                    </a>
                </li>
            </ul>

            <!-- Right Side - User Menu -->
            <ul class="navbar-nav ml-auto align-items-center">
                <?php if ($logged_in): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-white font-weight-bold" href="#" id="userDropdown" role="button" data-toggle="dropdown">
                            <i class="fas fa-user-circle fa-lg mr-1"></i>
                            Hi, <?php echo htmlspecialchars($fullname); ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow">
                            <a class="dropdown-item" href="profile.php">
                                <i class="fas fa-user-edit"></i> My Profile
                            </a>
                            <a class="dropdown-item" href="applications.php">
                                <i class="fas fa-file-alt"></i> My Applications
                            </a>
                            <?php if ($is_admin): ?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item text-danger font-weight-bold" href="admin/dashboard.php">
                                    <i class="fas fa-cog"></i> Admin Panel
                                </a>
                            <?php endif; ?>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-danger" href="logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </div>
                    </li>
                <?php else: ?>
                    <!-- Guest User -->
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-light text-primary ml-2 px-4" href="register.php">
                            <i class="fas fa-user-plus"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>