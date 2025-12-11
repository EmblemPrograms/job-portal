<?php include 'includes/user-navbar.php'; ?>

<?php
// Get job ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: jobs.php");
    exit();
}
$job_id = (int)$_GET['id'];

// Fetch job details
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $error = "Job not found!";
} else {
    $job = $result->fetch_assoc();
}
$stmt->close();

// Handle Application Submission
$apply_msg = '';
$apply_type = '';

if ($logged_in && $_SERVER["REQUEST_METHOD"] == "POST") {
    $cover_letter = trim($_POST['cover_letter']);
    $user_id = $_SESSION['user_id'];

    // Check if already applied
    $check = $conn->prepare("SELECT id FROM applications WHERE job_id = ? AND user_id = ?");
    $check->bind_param("ii", $job_id, $user_id);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        $apply_msg = "You have already applied for this job!";
        $apply_type = "warning";
    } else {
        $resume_path = '';
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] == 0) {
            $upload_dir = "uploads/resumes/";
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

            $file_name = "resume_" . $user_id . "_" . time() . "_" . basename($_FILES["resume"]["name"]);
            $target_file = $upload_dir . $file_name;

            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            if ($_FILES["resume"]["size"] > 5000000) {
                $apply_msg = "Resume file is too large (max 5MB).";
                $apply_type = "danger";
            } elseif ($file_type != "pdf") {
                $apply_msg = "Only PDF resumes are allowed.";
                $apply_type = "danger";
            } elseif (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_file)) {
                $resume_path = $target_file;
            } else {
                $apply_msg = "Error uploading resume.";
                $apply_type = "danger";
            }
        }

        if (empty($apply_msg)) {
            $insert = $conn->prepare("INSERT INTO applications (job_id, user_id, resume_path, cover_letter) VALUES (?, ?, ?, ?)");
            $insert->bind_param("iiss", $job_id, $user_id, $resume_path, $cover_letter);
            if ($insert->execute()) {
                $apply_msg = "Application submitted successfully!";
                $apply_type = "success";
            } else {
                $apply_msg = "Error submitting application.";
                $apply_type = "danger";
            }
            $insert->close();
        }
    }
    $check->close();
}

// Check if already applied (for display)
$already_applied = false;
if ($logged_in) {
    $check = $conn->prepare("SELECT id FROM applications WHERE job_id = ? AND user_id = ?");
    $check->bind_param("ii", $job_id, $_SESSION['user_id']);
    $check->execute();
    $check->store_result();
    $already_applied = $check->num_rows > 0;
    $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($job) ? htmlspecialchars($job['title']) : 'Job Details'; ?> - JobPortal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f8f9fa; }
        .job-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 12px 12px 0 0;
            padding: 2rem;
        }
        .job-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .info-tag {
            background: #e7f3ff;
            color: #007bff;
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            margin-right: 10px;
            margin-bottom: 10px;
            display: inline-block;
        }
        .apply-card {
            border-radius: 12px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .btn-apply {
            background: #28a745;
            border: none;
            padding: 12px 30px;
            font-size: 1.1rem;
            border-radius: 50px;
        }
        .btn-apply:hover {
            background: #218838;
        }
        .description-box {
            background: #f1f3f5;
            padding: 1.5rem;
            border-radius: 10px;
            line-height: 1.8;
        }
        @media (max-width: 768px) {
            .job-header h1 { font-size: 1.8rem; }
        }
    </style>
</head>
<body>

<div class="container my-5">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center py-5">
            <h3><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></h3>
            <a href="jobs.php" class="btn btn-primary btn-lg mt-3">Back to Jobs</a>
        </div>
    <?php else: ?>
        <div class="justify-content-center">
            <!-- Job Details -->
            <div class="col-lg-12 mb-4">
                <div class="card job-card">
                    <div class="job-header text-white">
                        <h1 class="display-5 font-weight-bold"><?php echo htmlspecialchars($job['title']); ?></h1>
                        <h4 class="mb-0"><?php echo htmlspecialchars($job['company']); ?></h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Job Info Tags -->
                        <div class="mb-4">
                            <span class="info-tag"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                            <span class="info-tag"><i class="fas fa-clock"></i> <?php echo htmlspecialchars($job['type'] ?? 'Full-time'); ?></span>
                            <span class="info-tag">&#8358</i> <?php echo htmlspecialchars($job['salary'] ?? 'Not disclosed'); ?></span>
                            <span class="info-tag"><i class="fas fa-chart-line"></i> <?php echo htmlspecialchars($job['experience_level'] ?? 'Not specified'); ?></span>
                        </div>

                        <h4 class="mt-5 mb-3">Job Description</h4>
                        <div class="description-box">
                            <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                        </div>

                        <?php if ($apply_msg): ?>
                    <div class="alert alert-<?php echo $apply_type; ?> text-center mb-4">
                        <h5><i class="fas <?php echo $apply_type === 'success' ? 'fa-check-circle' : 'fa-info-circle'; ?>"></i></h5>
                        <strong><?php echo $apply_msg; ?></strong>
                    </div>
                <?php endif; ?>

                <div class="text-center mb-4">
                    <a href="apply.php?job_id=1" class="btn btn-success">Quick Apply</a>
                </div>

                <div class="text-center mt-4">
                    <a href="jobs.php" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-arrow-left mr-2"></i> Back to Jobs
                    </a>
                </div>

                        <div class="mt-4 text-muted small">
                            <i class="fas fa-calendar-alt"></i> Posted on <?php echo date("F j, Y", strtotime($job['created_at'])); ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Show selected filename in custom file input
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    var fileName = e.target.files[0].name;
    e.target.nextElementSibling.innerText = fileName;
});
</script>
</body>
</html>