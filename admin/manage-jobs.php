<?php include '../includes/admin-navbar.php'; ?>

<?php
// Delete job (with confirmation)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $job_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $job_id);

    if ($stmt->execute()) {
        // Also delete related applications (optional)
        $conn->query("DELETE FROM applications WHERE job_id = $job_id");
        $msg = "Job deleted successfully!";
        $type = "success";
    } else {
        $msg = "Error deleting job.";
        $type = "danger";
    }
    $stmt->close();
}
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white d-flex justify-content-between align-items-center">
        <h4 class="m-0 font-weight-bold">
            <i class="fas fa-briefcase mr-2"></i> Manage Job Postings
        </h4>
        <a href="add-job.php" class="btn btn-light btn-sm">
            <i class="fas fa-plus"></i> Add New Job
        </a>
    </div>

    <div class="card-body">

        <?php if (isset($msg)): ?>
            <div class="alert alert-<?php echo $type; ?> alert-dismissible fade show">
                <strong><?php echo $msg; ?></strong>
                <button type="button" class="close" data-dismiss="alert">×</button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="jobsTable">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Location</th>
                        <th>Type</th>
                        <th>Salary</th>
                        <th>Applications</th>
                        <th>Posted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT j.*, COUNT(a.id) as app_count 
                            FROM jobs j 
                            LEFT JOIN applications a ON j.id = a.job_id 
                            GROUP BY j.id 
                            ORDER BY j.created_at DESC";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0):
                        $count = 1;
                        while ($job = $result->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($job['title']); ?></strong>
                                </td>
                                <td><?php echo htmlspecialchars($job['company']); ?></td>
                                <td>
                                    <span class="badge badge-info">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <?php echo htmlspecialchars($job['location']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?php
                                    echo $job['type'] == 'Full-time' ? 'success' :
                                        ($job['type'] == 'Part-time' ? 'warning' :
                                            ($job['type'] == 'Internship' ? 'info' : 'secondary')); ?>">
                                        <?php echo htmlspecialchars($job['type']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($job['salary'] ?: 'Not disclosed'); ?></td>
                                <td>
                                    <span class="badge badge-primary badge-lg">
                                        <?php echo $job['app_count']; ?> Applications
                                    </span>
                                </td>
                                <td><?php echo date("M j, Y", strtotime($job['created_at'])); ?></td>
                                <td>
                                    <a href="edit-job.php?id=<?php echo $job['id']; ?>" class="btn btn-warning btn-sm"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="delete-job.php?id=<?php echo $job['id']; ?>" class="btn btn-danger btn-sm"
                                        title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                    <a href="view-applications.php?job_id=<?php echo $job['id']; ?>" class="btn btn-info btn-sm"
                                        title="View Applications">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-briefcase fa-5x mb-4 text-muted"></i><br>
                                <h4>No jobs posted yet</h4>
                                <a href="add-job.php" class="btn btn-success mt-3">
                                    <i class="fas fa-plus"></i> Post Your First Job
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

</div> <!-- End .container-fluid -->
</div> <!-- End .main-content -->

<?php include '../includes/footer.php'; ?>
</body>

</html>