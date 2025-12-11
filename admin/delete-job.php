<?php include '../includes/admin-navbar.php'; ?>

<?php
// Check if job ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $msg = "Invalid job ID!";
    $type = "danger";
} else {
    $job_id = (int)$_GET['id'];

    // Optional: Confirm the job exists
    $check_stmt = $conn->prepare("SELECT title, company FROM jobs WHERE id = ?");
    $check_stmt->bind_param("i", $job_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows !== 1) {
        $msg = "Job not found!";
        $type = "danger";
    } else {
        $job = $result->fetch_assoc();
        $job_title = $job['title'];
        $company = $job['company'];

        // Perform deletion if confirmed
        if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
            $delete_stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
            $delete_stmt->bind_param("i", $job_id);

            if ($delete_stmt->execute()) {
                // Applications will be auto-deleted due to ON DELETE CASCADE (if foreign key set)
                $msg = "Job \"$job_title\" at $company has been deleted successfully!";
                $type = "success";
            } else {
                $msg = "Error deleting job.";
                $type = "danger";
            }
            $delete_stmt->close();
        }
    }
    $check_stmt->close();
}
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-danger text-white">
        <h4 class="m-0 font-weight-bold">
            <i class="fas fa-trash-alt mr-2"></i> Delete Job Posting
        </h4>
    </div>
    <div class="card-body text-center">

        <?php if (isset($msg) && $type === "success"): ?>
            <div class="alert alert-success">
                <strong><?php echo $msg; ?></strong>
            </div>
            <a href="manage-jobs.php" class="btn btn-primary btn-lg mt-3">
                <i class="fas fa-arrow-left"></i> Back to Manage Jobs
            </a>

        <?php elseif (isset($msg) && $type === "danger"): ?>
            <div class="alert alert-danger">
                <strong><?php echo $msg; ?></strong>
            </div>
            <a href="manage-jobs.php" class="btn btn-secondary btn-lg mt-3">
                Back to Manage Jobs
            </a>

        <?php elseif (isset($job_title)): ?>
            <!-- Confirmation Prompt -->
            <i class="fas fa-exclamation-triangle fa-5x text-danger mb-4"></i>
            <h3>Are you sure?</h3>
            <p class="lead">You are about to <strong>permanently delete</strong> the following job:</p>

            <div class="alert alert-warning">
                <h5><strong><?php echo htmlspecialchars($job_title); ?></strong></h5>
                <p class="mb-0"><strong>Company:</strong> <?php echo htmlspecialchars($company); ?></p>
            </div>

            <p class="text-danger font-weight-bold">
                This action <u>cannot be undone</u>.<br>
                All applications for this job will also be deleted.
            </p>

            <div class="mt-4">
                <a href="?id=<?php echo $job_id; ?>&confirm=yes" class="btn btn-danger btn-lg px-5">
                    <i class="fas fa-trash"></i> Yes, Delete It
                </a>
                <a href="manage-jobs.php" class="btn btn-secondary btn-lg px-5 ml-3">
                    <i class="fas fa-ban"></i> Cancel
                </a>
            </div>
        <?php endif; ?>

    </div>
</div>

</div> <!-- End .container-fluid -->
</div> <!-- End .main-content -->

<?php include '../includes/footer.php'; ?>
</body>
</html>