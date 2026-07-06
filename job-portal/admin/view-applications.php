<?php include '../includes/admin-navbar.php'; ?>

<?php
// Get job ID from URL
if (!isset($_GET['job_id']) || !is_numeric($_GET['job_id'])) {
    header("Location: manage-jobs.php");
    exit();
}
$job_id = (int)$_GET['job_id'];

// Fetch job details for header
$stmt = $conn->prepare("SELECT title, company, location FROM jobs WHERE id = ?");
$stmt->bind_param("i", $job_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $msg = "Job not found!";
    $type = "danger";
} else {
    $job = $result->fetch_assoc();
    $job_title = $job['title'];
    $company = $job['company'];
    $location = $job['location'];
}
$stmt->close();
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-info text-white d-flex justify-content-between align-items-center">
        <h4 class="m-0 font-weight-bold">
            <i class="fas fa-file-alt mr-2"></i> Applications for "<?php echo htmlspecialchars($job_title); ?>"
        </h4>
        <div>
            <span class="badge badge-light badge-lg mr-3">
                <?php echo htmlspecialchars($company); ?> | <?php echo htmlspecialchars($location); ?>
            </span>
            <a href="manage-jobs.php" class="btn btn-light btn-sm">
                <i class="fas fa-arrow-left"></i> Back to Jobs
            </a>
        </div>
    </div>

    <div class="card-body">

        <?php if (isset($msg)): ?>
            <div class="alert alert-<?php echo $type; ?>">
                <strong><?php echo $msg; ?></strong>
            </div>
        <?php else: ?>

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="thead-dark">
                        <tr>
                            <th>#</th>
                            <th>Applicant</th>
                            <th>Contact</th>
                            <th>Applied On</th>
                            <th>Resume</th>
                            <th>Cover Letter</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT 
                                    a.id AS app_id,
                                    a.resume_path,
                                    a.cover_letter,
                                    a.applied_at,
                                    u.id AS user_id,
                                    u.fullname,
                                    u.username,
                                    u.email,
                                    u.phone
                                FROM applications a
                                JOIN users u ON a.user_id = u.id
                                WHERE a.job_id = ?
                                ORDER BY a.applied_at DESC";

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("i", $job_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0):
                            $count = 1;
                            while ($app = $result->fetch_assoc()):
                                $resume_exists = !empty($app['resume_path']) && file_exists('../' . $app['resume_path']);
                        ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($app['fullname'] ?: $app['username']); ?></strong>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($app['email']); ?><br>
                                    <small class="text-muted"><?php echo htmlspecialchars($app['phone'] ?: '—'); ?></small>
                                </td>
                                <td><?php echo date("M j, Y", strtotime($app['applied_at'])); ?></td>
                                <td>
                                    <?php if ($resume_exists): ?>
                                        <a href="../<?php echo $app['resume_path']; ?>" target="_blank" class="btn btn-success btn-sm">
                                            <i class="fas fa-file-pdf"></i> View Resume
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">No resume</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($app['cover_letter'])): ?>
                                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#coverModal<?php echo $app['app_id']; ?>">
                                            <i class="fas fa-envelope-open-text"></i> View
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">None</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="view-user.php?id=<?php echo $app['user_id']; ?>" class="btn btn-primary btn-sm" title="View Profile">
                                        <i class="fas fa-user"></i>
                                    </a>
                                </td>
                            </tr>

                            <!-- Cover Letter Modal -->
                            <?php if (!empty($app['cover_letter'])): ?>
                            <div class="modal fade" id="coverModal<?php echo $app['app_id']; ?>">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title">Cover Letter - <?php echo htmlspecialchars($app['fullname'] ?: $app['username']); ?></h5>
                                            <button type="button" class="close" data-dismiss="modal">×</button>
                                        </div>
                                        <div class="modal-body">
                                            <p><?php echo nl2br(htmlspecialchars($app['cover_letter'])); ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                        <?php
                            endwhile;
                        else:
                        ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fas fa-inbox fa-5x mb-4"></i><br>
                                    <h4>No applications yet</h4>
                                    <p>This job hasn't received any applications.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php $stmt->close(); ?>
                    </tbody>
                </table>
            </div>

        <?php endif; ?>
    </div>
</div>

</div> <!-- End .container-fluid -->
</div> <!-- End .main-content -->

<?php include '../includes/footer.php'; ?>
</body>
</html>