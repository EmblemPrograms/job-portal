<?php
// edit-application.php

session_start();
include 'includes/connect.php';  // your DB connection file (defines $conn)

if (!isset($_SESSION['user_id']) || !is_numeric($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$app_id  = isset($_GET['app_id']) ? (int)$_GET['app_id'] : 0;

if ($app_id <= 0) {
    $_SESSION['error'] = "Invalid application ID.";
    header("Location: my-applications.php");
    exit;
}

// Fetch application + job title — only if still pending and belongs to this user
$sql = "SELECT a.id, a.resume_path, a.cover_letter, a.status, 
               j.title, j.company 
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        WHERE a.id = ? AND a.user_id = ? 
          AND (a.status = 'pending' OR a.status IS NULL)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $app_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Application not found, already processed, or you don't have permission to edit it.";
    header("Location: my-applications.php");
    exit;
}

$app = $result->fetch_assoc();
$stmt->close();

// ────────────────────────────────────────────────
// HANDLE FORM SUBMISSION
// ────────────────────────────────────────────────
$errors   = [];
$success  = false;
$upload_dir = "uploads/resumes/";  // make sure this folder exists + is writable (chmod 755 or 775)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cover_letter = trim($_POST['cover_letter'] ?? '');

    // ── Resume upload (optional) ───────────────────────
    $new_resume_path = $app['resume_path']; // keep old one by default

    if (!empty($_FILES['resume']['name'])) {
        $file = $_FILES['resume'];
        $allowed = ['pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
            $errors[] = "Only PDF, DOC, DOCX files are allowed.";
        } elseif ($file['size'] > 5 * 1024 * 1024) { // 5MB max
            $errors[] = "File size must be less than 5MB.";
        } else {
            $filename = $user_id . '_' . time() . '.' . $ext;
            $target   = $upload_dir . $filename;

            if (move_uploaded_file($file['tmp_name'], $target)) {
                // Delete old resume if exists and different
                if ($app['resume_path'] && file_exists($app['resume_path']) && $app['resume_path'] !== $target) {
                    @unlink($app['resume_path']);
                }
                $new_resume_path = $target;
            } else {
                $errors[] = "Failed to upload resume. Check folder permissions.";
            }
        }
    }

    // ── Save changes if no errors ───────────────────────
    if (empty($errors)) {
        $update_sql = "UPDATE applications 
                       SET resume_path = ?, cover_letter = ?
                       WHERE id = ? AND user_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssii", $new_resume_path, $cover_letter, $app_id, $user_id);

        if ($update_stmt->execute()) {
            $success = true;
            $_SESSION['success'] = "Application updated successfully!";
            // Refresh data
            $app['resume_path']   = $new_resume_path;
            $app['cover_letter']  = $cover_letter;
        } else {
            $errors[] = "Database error: " . $update_stmt->error;
        }
        $update_stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Application - JobPortal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .form-container { max-width: 800px; margin: 40px auto; }
        .card-header { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; }
    </style>
</head>
<body>

<?php include 'includes/user-navbar.php'; ?>

<div class="container form-container">
    <div class="card shadow">
        <div class="card-header py-3">
            <h4 class="m-0">Edit Application</h4>
            <small>For: <strong><?php echo htmlspecialchars($app['title']); ?></strong> at <?php echo htmlspecialchars($app['company']); ?></small>
        </div>

        <div class="card-body">

            <?php if ($success): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    <button type="button" class="close" data-dismiss="alert">×</button>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $err): ?>
                            <li><?php echo htmlspecialchars($err); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <!-- Resume Upload -->
                <div class="form-group">
                    <label for="resume">Resume <small class="text-muted">(PDF, DOC, DOCX • max 5MB)</small></label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="resume" name="resume" accept=".pdf,.doc,.docx">
                        <label class="custom-file-label" for="resume">Choose file...</label>
                    </div>
                    <?php if ($app['resume_path'] && file_exists($app['resume_path'])): ?>
                        <small class="form-text text-muted">
                            Current: <a href="<?php echo $app['resume_path']; ?>" target="_blank">View current resume</a>
                        </small>
                    <?php endif; ?>
                </div>

                <!-- Cover Letter -->
                <div class="form-group">
                    <label for="cover_letter">Cover Letter <small class="text-muted">(optional)</small></label>
                    <textarea class="form-control" id="cover_letter" name="cover_letter" rows="8"><?php 
                        echo htmlspecialchars($app['cover_letter'] ?? ''); 
                    ?></textarea>
                    <small class="form-text text-muted">You can update or completely change your cover letter.</small>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="applications.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to My Applications
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Custom file input label update
document.querySelector('.custom-file-input').addEventListener('change', function(e) {
    let fileName = e.target.files[0]?.name || 'Choose file...';
    e.target.nextElementSibling.innerText = fileName;
});
</script>
</body>
</html>

<?php $conn->close(); ?>