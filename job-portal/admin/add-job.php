<?php include '../includes/admin-navbar.php'; ?>

<?php
$message = '';
$type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title       = trim($_POST['title']);
    $company     = trim($_POST['company']);
    $location    = trim($_POST['location']);
    $salary      = trim($_POST['salary']);
    $type        = $_POST['type']; // Full-time, Part-time, etc.
    $experience  = $_POST['experience'];
    $description = trim($_POST['description']);

    // Basic validation
    if (empty($title) || empty($company) || empty($location) || empty($description)) {
        $message = "Title, Company, Location and Description are required!";
        $type = "danger";
    } else {
        $stmt = $conn->prepare("INSERT INTO jobs (title, company, location, salary, type, experience_level, description) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $title, $company, $location, $salary, $type, $experience, $description);

        if ($stmt->execute()) {
            $message = "New job posted successfully!";
            $type = "success";
            // Clear form
            $_POST = array();
        } else {
            $message = "Error: " . $stmt->error;
            $type = "danger";
        }
        $stmt->close();
    }
}
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-success text-white">
        <h4 class="m-0 font-weight-bold">
            <i class="fas fa-plus-circle mr-2"></i> Add New Job Posting
        </h4>
    </div>
    <div class="card-body">

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $type; ?> alert-dismissible fade show">
                <strong><?php echo $message; ?></strong>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-heading"></i> Job Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control form-control-lg" 
                           placeholder="e.g. Senior PHP Developer" value="<?php echo $_POST['title'] ?? ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-building"></i> Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="company" class="form-control form-control-lg" 
                           placeholder="e.g. Tech Solutions Pvt Ltd" value="<?php echo $_POST['company'] ?? ''; ?>" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-map-marker-alt"></i> Location <span class="text-danger">*</span></label>
                    <input type="text" name="location" class="form-control form-control-lg" 
                           placeholder="e.g. Lagos, Osun or Remote" value="<?php echo $_POST['location'] ?? ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"> &#8358 Salary (Optional)</label>
                    <input type="text" name="salary" class="form-control form-control-lg" 
                           placeholder="e.g. &#8358 80,000-150,000 or Not Disclosed" value="<?php echo $_POST['salary'] ?? ''; ?>">
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-clock"></i> Job Type</label>
                    <select name="type" class="form-control form-control-lg">
                        <option value="Full-time" <?php echo ($_POST['type'] ?? '') == 'Full-time' ? 'selected' : ''; ?>>Full-time</option>
                        <option value="Part-time" <?php echo ($_POST['type'] ?? '') == 'Part-time' ? 'selected' : ''; ?>>Part-time</option>
                        <option value="Contract" <?php echo ($_POST['type'] ?? '') == 'Contract' ? 'selected' : ''; ?>>Contract</option>
                        <option value="Internship" <?php echo ($_POST['type'] ?? '') == 'Internship' ? 'selected' : ''; ?>>Internship</option>
                        <option value="Freelance" <?php echo ($_POST['type'] ?? '') == 'Freelance' ? 'selected' : ''; ?>>Freelance</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="font-weight-bold"><i class="fas fa-chart-line"></i> Experience Level</label>
                    <select name="experience" class="form-control form-control-lg">
                        <option value="Fresher" <?php echo ($_POST['experience'] ?? '') == 'Fresher' ? 'selected' : ''; ?>>Fresher (0-1 year)</option>
                        <option value="Junior" <?php echo ($_POST['experience'] ?? '') == 'Junior' ? 'selected' : ''; ?>>Junior (1-3 years)</option>
                        <option value="Mid-level" <?php echo ($_POST['experience'] ?? '') == 'Mid-level' ? 'selected' : ''; ?>>Mid-level (3-6 years)</option>
                        <option value="Senior" <?php echo ($_POST['experience'] ?? '') == 'Senior' ? 'selected' : ''; ?>>Senior (6+ years)</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="font-weight-bold"><i class="fas fa-file-alt"></i> Job Description <span class="text-danger">*</span></label>
                <textarea name="description" class="form-control" rows="10" 
                          placeholder="Describe the role, responsibilities, required skills, qualifications..." required><?php echo $_POST['description'] ?? ''; ?></textarea>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg px-5">
                    <i class="fas fa-paper-plane mr-2"></i> Publish Job
                </button>
                <a href="dashboard.php" class="btn btn-secondary btn-lg px-5 ml-3">
                    <i class="fas fa-arrow-left"></i> Back to Dashboard
                </a>
            </div>
        </form>
    </div>
</div>

</div> <!-- End .container-fluid -->
</div> <!-- End .main-content -->

<?php include '../includes/footer.php'; ?>
</body>
</html>