<?php include '../includes/admin-navbar.php'; ?>

<?php
// Get job ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage-jobs.php");
    exit();
}

$job_id = $_GET['id'];

// Fetch job details
$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $msg = "Job not found!";
    $type = "danger";
} else {
    $job = $result->fetch_assoc();
}
$stmt->close();

$message = '';
$type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title       = trim($_POST['title']);
    $company     = trim($_POST['company']);
    $location    = trim($_POST['location']);
    $salary      = trim($_POST['salary']);
    $type_job    = $_POST['type'];
    $experience  = $_POST['experience'];
    $description = trim($_POST['description']);

    // Validation
    if (empty($title) || empty($company) || empty($location) || empty($description)) {
        $message = "Title, Company, Location and Description are required!";
        $type = "danger";
    } else {
        $update_stmt = $conn->prepare("UPDATE jobs SET 
            title = ?, company = ?, location = ?, salary = ?, type = ?, experience_level = ?, description = ? 
            WHERE id = ?");
        $update_stmt->bind_param("sssssssi", $title, $company, $location, $salary, $type_job, $experience, $description, $job_id);

        if ($update_stmt->execute()) {
            $message = "Job updated successfully!";
            $type = "success";
            // Refresh job data
            $stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
            $stmt->bind_param("i", $job_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $job = $result->fetch_assoc();
            $stmt->close();
        } else {
            $message = "Error updating job.";
            $type = "danger";
        }
        $update_stmt->close();
    }
}
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h4 class="m-0 font-weight-bold">
            <i class="fas fa-edit mr-2"></i> Edit Job Posting
        </h4>
    </div>
    <div class="card-body">

        <?php if (isset($msg)): ?>
            <div class="alert alert-danger">
                <strong><?php echo $msg; ?></strong>
            </div>
            <a href="manage-jobs.php" class="btn btn-secondary">Back to Manage Jobs</a>
        <?php elseif ($message): ?>
            <div class="alert alert-<?php echo $type; ?> alert-dismissible fade show">
                <strong><?php echo $message; ?></strong>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (isset($job)): ?>
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-heading"></i> Job Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg" 
                           value="<?php echo htmlspecialchars($job['title']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-building"></i> Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="company" class="form-control form-control-lg" 
                           value="<?php echo htmlspecialchars($job['company']); ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-map-marker-alt"></i> Location <span class="text-danger">*</span></label>
                    <input type="text" name="location" class="form-control form-control-lg" 
                           value="<?php echo htmlspecialchars($job['location']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-rupee-sign"></i> Salary (Optional)</label>
                    <input type="text" name="salary" class="form-control form-control-lg" 
                           value="<?php echo htmlspecialchars($job['salary'] ?? ''); ?>" placeholder="e.g. ₹15-25 LPA">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-clock"></i> Job Type</label>
                    <select name="type" class="form-control form-control-lg">
                        <option value="Full-time" <?php echo ($job['type'] ?? '') == 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
                        <option value="Part-time" <?php echo ($job['type'] ?? '') == 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
                        <option value="Contract" <?php echo ($job['type'] ?? '') == 'Contract' ? 'selected' : ''; ?>>Contract</option>
                        <option value="Internship" <?php echo ($job['type'] ?? '') == 'Internship' ? 'selected' : ''; ?>>Internship</option>
                        <option value="Freelance" <?php echo ($job['type'] ?? '') == 'Freelance' ? 'selected' : ''; ?>>Freelance</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-chart-line"></i> Experience Level</label>
                    <select name="experience" class="form-control form-control-lg">
                        <option value="Fresher" <?php echo ($job['experience_level'] ?? '') == 'Fresher' ? 'selected' : ''; ?>>Fresher (0-1 year)</option>
                        <option value="Junior" <?php echo ($job['experience_level'] ?? '') == 'Junior' ? 'selected' : ''; ?>>Junior (1-3 years)</option>
                        <option value="Mid-level" <?php echo ($job['experience_level'] ?? '') == 'Mid-level' ? 'selected' : ''; ?>>Mid-level (3-6 years)</option>
                        <option value="Senior" <?php echo ($job['experience_level'] ?? '') == 'Senior' ? 'selected' : ''; ?>>Senior (6+ years)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="font-weight-bold"><i class="fas fa-file-alt"></i> Job Description <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="12" required><?php echo htmlspecialchars($job['description']); ?></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-save mr-2"></i> Update Job
                </button>
                <a href="manage-jobs.php" class="btn btn-secondary btn-lg px-5 ml-3">
                    <i class="fas fa-arrow-left"></i> Back to Manage Jobs
                </a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</div>

</div> <!-- End .container-fluid -->
</div> <!-- End .main-content -->

<?php include '../includes/footer.php'; ?>
</body>
</html>