<?php
// Enable error reporting (remove these 2 lines later when everything works)
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'includes/connect.php';

// Only redirect if user is REALLY logged in
if (!empty($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
    // Optional: double-check the session is valid in database
    $stmt = $conn->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
        header("Location: index.php");
        exit();
    } else {
        // Invalid session → destroy it
        session_destroy();
    }
    $stmt->close();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        $error = "Please enter username/email and password!";
    } else {
        // Search by username OR email
        $stmt = $conn->prepare("SELECT id, username, fullname, password, role FROM users WHERE username = ? OR email = ? LIMIT 1");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                // Login Success!
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['fullname']  = $user['fullname'] ?? $user['username'];
                $_SESSION['role']      = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: index.php");
                }
                exit();
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "Username or email not found!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - JobPortal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            overflow: hidden;
            background: white;
        }
        .card-header {
            background: #007bff;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .btn-primary {
            background: #007bff;
            border: none;
            border-radius: 50px;
            padding: 12px;
            font-weight: bold;
        }
        .form-control {
            border-radius: 10px;
            padding: 12px 15px;
        }
    </style>
</head>
<body class="d-flex align-items-center min-vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="login-card">
                <div class="card-header">
                    <h3><i class="fas fa-briefcase fa-2x mb-3"></i><br>JobPortal Login</h3>
                </div>
                <div class="card-body p-5">

                    <?php if($error): ?>
                        <div class="alert alert-danger text-center">
                            <strong><?php echo $error; ?></strong>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group">
                            <label><i class="fas fa-user"></i> Username or Email</label>
                            <input type="text" name="username" class="form-control form-control-lg" 
                                   placeholder="Enter username or email"  required autofocus>
                        </div>

                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Password</label>
                            <input type="password" name="password" class="form-control form-control-lg" 
                                   placeholder="Enter your password" required>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-sign-in-alt"></i> Login Now
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <p>Don't have an account? 
                            <a href="register.php" class="text-success font-weight-bold">Register as Jobseeker</a>
                        </p>
                        <p>
                            <a href="index.php" class="text-muted"><i class="fas fa-home"></i> Back to Home</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>