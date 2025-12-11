<?php
session_start();
include 'includes/connect.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize inputs
    $fullname     = trim($_POST['fullname']);
    $username     = trim($_POST['username']);
    $email        = trim($_POST['email']);
    $phone        = trim($_POST['phone']);
    $location     = trim($_POST['location']);
    $experience   = $_POST['experience'];
    $education    = $_POST['education'];
    $skills       = trim($_POST['skills']);
    $password     = $_POST['password'];
    $confirm      = $_POST['confirm_password'];

    // Validation
    if (empty($fullname) || empty($username) || empty($email) || empty($password) || empty($phone)) {
        $error = "Full Name, Username, Email, Phone & Password are required!";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match!";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters!";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or Email is already taken!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new jobseeker with full profile
            $insert = $conn->prepare("INSERT INTO users 
                (fullname, username, email, phone, location, experience, education, skills, password, role) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'user')");

            $insert->bind_param("sssssssss", $fullname, $username, $email, $phone, $location, 
                              $experience, $education, $skills, $hashed_password);

            if ($insert->execute()) {
                $success = "Account created successfully! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
            $insert->close();
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jobseeker Registration - JobPortal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
        .register-card { border-radius: 20px; box-shadow: 0 15px 35px rgba(0,0,0,0.3); overflow: hidden; }
        .card-header { background: #28a745; color: white; padding: 2rem; text-align: center; }
        .form-control { border-radius: 10px; }
        .btn-success { border-radius: 50px; padding: 12px; font-weight: bold; }
    </style>
</head>
<body class="d-flex align-items-center">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card register-card">
                <div class="card-header">
                    <h3><i class="fas fa-user-graduate mr-3"></i>Join as a Jobseeker</h3>
                    <p class="mb-0">Create your profile and start applying today!</p>
                </div>
                <div class="card-body p-5">

                    <?php if($success): ?>
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle fa-2x"></i><br><br>
                            <strong><?php echo $success; ?></strong>
                            <hr>
                            <a href="login.php" class="btn btn-success btn-lg">Go to Login</a>
                        </div>
                    <?php else: ?>

                        <?php if($error): ?>
                            <div class="alert alert-danger text-center">
                                <i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-user"></i> Full Name</label>
                                    <input type="text" name="fullname" class="form-control form-control-lg" placeholder="John Doe" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-user-tag"></i> Username</label>
                                    <input type="text" name="username" class="form-control form-control-lg" placeholder="john_doe123" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-envelope"></i> Email</label>
                                    <input type="email" name="email" class="form-control form-control-lg" placeholder="john@example.com" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-phone"></i> Phone Number</label>
                                    <input type="text" name="phone" class="form-control form-control-lg" placeholder="+91 9876543210" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-map-marker-alt"></i> Current Location</label>
                                    <input type="text" name="location" class="form-control form-control-lg" placeholder="Mumbai, India">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-briefcase"></i> Experience</label>
                                    <select name="experience" class="form-control form-control-lg">
                                        <option value="Fresher">Fresher (0 years)</option>
                                        <option value="0-1 year">0-1 year</option>
                                        <option value="1-3 years">1-3 years</option>
                                        <option value="3-5 years">3-5 years</option>
                                        <option value="5+ years">5+ years</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label><i class="fas fa-graduation-cap"></i> Highest Education</label>
                                <input type="text" name="education" class="form-control form-control-lg" placeholder="e.g., B.Tech in Computer Science">
                            </div>

                            <div class="mb-3">
                                <label><i class="fas fa-tools"></i> Key Skills (comma separated)</label>
                                <textarea name="skills" class="form-control" rows="3" placeholder="PHP, Laravel, JavaScript, MySQL, Bootstrap, Git..."></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-lock"></i> Password</label>
                                    <input type="password" name="password" class="form-control form-control-lg" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label><i class="fas fa-lock"></i> Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control form-control-lg" required>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" class="btn btn-success btn-lg px-5">
                                    <i class="fas fa-user-plus"></i> Create Jobseeker Account
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p>Already have an account? <a href="login.php" class="text-success font-weight-bold">Login here</a></p>
                            <p><a href="index.php" class="text-muted">Back to Home</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>