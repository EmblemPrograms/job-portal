<?php include 'includes/user-navbar.php'; ?>

<?php
// Redirect if not logged in
if (!$logged_in) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$type = '';

// Fetch current user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname   = trim($_POST['fullname']);
    $phone      = trim($_POST['phone']);
    $location   = trim($_POST['location']);
    $experience = $_POST['experience'];
    $education  = trim($_POST['education']);
    $skills     = trim($_POST['skills']);

    $update = $conn->prepare("UPDATE users SET 
        fullname = ?, phone = ?, location = ?, experience = ?, education = ?, skills = ? 
        WHERE id = ?");
    $update->bind_param("ssssssi", $fullname, $phone, $location, $experience, $education, $skills, $user_id);

    if ($update->execute()) {
        $message = "Profile updated successfully!";
        $type = "success";
        // Refresh user data
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        // Update session fullname
        $_SESSION['fullname'] = !empty($user['fullname']) ? $user['fullname'] : $user['username'];
    } else {
        $message = "Error updating profile.";
        $type = "danger";
    }
    $update->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - JobPortal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f8f9fa; }
        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            border-radius: 0 0 20px 20px;
        }
        .profile-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .form-control, .custom-select {
            border-radius: 10px;
        }
        .btn-update {
            background: #28a745;
            border: none;
            padding: 12px 40px;
            border-radius: 50px;
            font-weight: bold;
        }
        .btn-update:hover {
            background: #218838;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: #fff;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="profile-header text-center">
    <div class="container">
        <div class="profile-avatar rounded-circle mx-auto d-flex align-items-center justify-content-center">
            <i class="fas fa-user fa-4x text-primary"></i>
        </div>
        <h1 class="mt-3 font-weight-bold"><?php echo htmlspecialchars($user['fullname'] ?: $user['username']); ?></h1>
        <p class="lead"><?php echo htmlspecialchars($user['email']); ?></p>
    </div>
</div>

<div class="container my-5">
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $type; ?> alert-dismissible fade show text-center">
            <strong><?php echo $message; ?></strong>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    <?php endif; ?>

    <div class="card profile-card">
        <div class="card-header bg-primary text-white text-center py-4">
            <h3 class="mb-0"><i class="fas fa-user-edit mr-2"></i> Edit Your Profile</h3>
            <p>Keep your profile updated to increase your chances with employers!</p>
        </div>
        <div class="card-body p-5">
            <form method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold"><i class="fas fa-user"></i> Full Name</label>
                        <input type="text" name="fullname" class="form-control form-control-lg" 
                               value="<?php echo htmlspecialchars($user['fullname'] ?? ''); ?>" placeholder="John Doe">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold"><i class="fas fa-envelope"></i> Email (cannot change)</label>
                        <input type="email" class="form-control form-control-lg bg-light" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold"><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="text" name="phone" class="form-control form-control-lg" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="+91 9876543210">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold"><i class="fas fa-map-marker-alt"></i> Current Location</label>
                        <input type="text" name="location" class="form-control form-control-lg" 
                               value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>" placeholder="Mumbai, India">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold"><i class="fas fa-chart-line"></i> Experience Level</label>
                        <select name="experience" class="form-control form-control-lg">
                            <option value="">-- Select --</option>
                            <option value="Fresher" <?php echo ($user['experience'] ?? '') == 'Fresher' ? 'selected' : ''; ?>>Fresher (0-1 year)</option>
                            <option value="Junior" <?php echo ($user['experience'] ?? '') == 'Junior' ? 'selected' : ''; ?>>Junior (1-3 years)</option>
                            <option value="Mid-level" <?php echo ($user['experience'] ?? '') == 'Mid-level' ? 'selected' : ''; ?>>Mid-level (3-6 years)</option>
                            <option value="Senior" <?php echo ($user['experience'] ?? '') == 'Senior' ? 'selected' : ''; ?>>Senior (6+ years)</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="font-weight-bold"><i class="fas fa-graduation-cap"></i> Highest Education</label>
                        <input type="text" name="education" class="form-control form-control-lg" 
                               value="<?php echo htmlspecialchars($user['education'] ?? ''); ?>" placeholder="B.Tech in Computer Science">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="font-weight-bold"><i class="fas fa-tools"></i> Key Skills <small class="text-muted">(comma separated)</small></label>
                    <textarea name="skills" class="form-control" rows="4" placeholder="PHP, Laravel, MySQL, JavaScript, Bootstrap, Git..."><?php echo htmlspecialchars($user['skills'] ?? ''); ?></textarea>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-update text-white btn-lg px-5">
                        <i class="fas fa-save mr-2"></i> Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="applications.php" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-file-alt mr-2"></i> View My Applications
        </a>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>