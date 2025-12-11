<?php include 'includes/user-navbar.php'; ?>

<?php
// Redirect if no job_id
if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    header("Location: jobs.php");
    exit();
}
$job_id = (int)$_GET['job_id'];

// Fetch job details
$stmt = $conn->prepare("SELECT title, company, location FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $error = "Invalid or deleted job.";
} else {
    $job = $result->fetch_assoc();
}
$stmt->close();

// Only logged-in users can apply
if (!$logged_in) {
    header("Location: login.php?redirect=apply.php?job_id=$job_id");
    exit();
}

$apply_msg = '';
$apply_type = '';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
        $resume_path = null;

        // Handle resume upload
        if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "uploads/resumes/";
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            $original_name = basename($_FILES["resume"]["name"]);
            $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $new_filename = "resume_" . $user_id . "_" . time() . "." . $file_ext;
            $target_path = $upload_dir . $new_filename;

            // Validate file
            if ($_FILES["resume"]["size"] > 5000000) { // 5MB
                $apply_msg = "Resume file is too large (max 5MB).";
                $apply_type = "danger";
            } elseif ($file_ext !== "pdf") {
                $apply_msg = "Only PDF files are allowed.";
                $apply_type = "danger";
            } elseif (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_path)) {
                $resume_path = $target_path;
            } else {
                $apply_msg = "Failed to upload resume.";
                $apply_type = "danger";
            }
        } else {
            // Optional: Allow application without resume (or make required)
            // For now, allow without resume
        }

        // Insert application if no error
        if (empty($apply_msg)) {
            $insert = $conn->prepare("INSERT INTO applications (job_id, user_id, resume_path, cover_letter) VALUES (?, ?, ?, ?)");
            $insert->bind_param("iiss", $job_id, $user_id, $resume_path, $cover_letter);

            if ($insert->execute()) {
                $apply_msg = "Your application has been submitted successfully!";
                $apply_type = "success";
            } else {
                $apply_msg = "Error submitting application. Please try again.";
                $apply_type = "danger";
            }
            $insert->close();
        }
    }
    $check->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for <?php echo isset($job) ? htmlspecialchars($job['title']) : 'Job'; ?> - JobPortal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .apply-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
        }
        .job-summary {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .apply-form-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        .form-control, .custom-file-label {
            border-radius: 10px;
        }
        .btn-submit {
            background: #28a745;
            border: none;
            padding: 12px 40px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: bold;
        }
        .btn-submit:hover {
            background: #218838;
        }
        .alert {
            border-radius: 12px;
        }
    </style>
</head>
<body>

<div class="apply-container">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center">
            <h4><i class="fas fa-exclamation-triangle"></i> <?php echo $error; ?></h4>
            <a href="jobs.php" class="btn btn-primary btn-lg mt-3">Back to Jobs</a>
        </div>
    <?php else: ?>
        <!-- Job Summary -->
        <div class="job-summary text-center">
            <h2 class="text-primary font-weight-bold"><?php echo htmlspecialchars($job['title']); ?></h2>
            <h4 class="text-muted"><?php echo htmlspecialchars($job['company']); ?></h4>
            <p class="lead"><i class="fas fa-map-marker-alt text-info"></i> <?php echo htmlspecialchars($job['location']); ?></p>
        </div>

        <!-- Messages -->
        <?php if ($apply_msg): ?>
            <div class="alert alert-<?php echo $apply_type; ?> text-center">
                <h5><?php echo $apply_msg; ?></h5>
                <?php if ($apply_type === 'success'): ?>
                    <a href="jobs.php" class="btn btn-primary mt-3">Browse More Jobs</a>
                    <a href="applications.php" class="btn btn-success mt-3 ml-2">View My Applications</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (empty($apply_msg) || $apply_type !== 'success'): ?>
            <div class="apply-form-card">
                <h3 class="text-center mb-4"><i class="fas fa-paper-plane text-success"></i> Submit Your Application</h3>
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label class="font-weight-bold">Cover Letter <small class="text-muted">(Optional)</small></label>
                        <textarea name="cover_letter" class="form-control" rows="8" placeholder="Explain why you are the best fit for this role, your relevant experience, and enthusiasm for the position..."></textarea>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">Upload Your Resume <small class="text-muted">(PDF only, max 5MB)</small></label>
                        <div class="custom-file">
                            <input type="file" name="resume" accept=".pdf" class="custom-file-input" id="resumeInput">
                            <label class="custom-file-label" for="resumeInput">Choose PDF file...</label>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-submit text-white">
                            <i class="fas fa-paper-plane mr-2"></i> Submit Application
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="job-details.php?id=<?php echo $job_id; ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Job Details
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Update file input label with selected filename
document.getElementById('resumeInput').addEventListener('change', function() {
    var fileName = this.files[0]?.name || "Choose PDF file...";
    this.nextElementSibling.innerText = fileName;
});
</script>
</body>
</html>