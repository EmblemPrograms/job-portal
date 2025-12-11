<?php include '../includes/admin-navbar.php'; ?>

<?php
// Get user ID from URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: manage-users.php");
    exit();
}
$user_id = (int)$_GET['id'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND role = 'user'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $error = "Jobseeker not found!";
} else {
    $user = $result->fetch_assoc();
}
$stmt->close();
?>

<div class="container-fluid mt-4">
    <?php if (isset($error)): ?>
        <div class="alert alert-danger text-center">
            <h4><?php echo $error; ?></h4>
            <a href="manage-users.php" class="btn btn-primary">Back to Jobseekers</a>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- User Profile Card -->
            <div class="col-lg-4 mb-4">
                <div class="card shadow border-0">
                    <div class="card-body text-center p-5">
                        <div class="avatar mx-auto mb-4" style="width: 150px; height: 150px; background: #e9ecef; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user fa-5x text-muted"></i>
                        </div>
                        <h3 class="font-weight-bold"><?php echo htmlspecialchars($user['fullname'] ?: $user['username']); ?></h3>
                        <p class="text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                        <hr>
                        <div class="text-left">
                            <p><strong><i class="fas fa-phone text-primary"></i> Phone:</strong><br>
                                <?php echo htmlspecialchars($user['phone'] ?: 'Not provided'); ?></p>
                            <p><strong><i class="fas fa-map-marker-alt text-success"></i> Location:</strong><br>
                                <?php echo htmlspecialchars($user['location'] ?: 'Not provided'); ?></p>
                            <p><strong><i class="fas fa-chart-line text-warning"></i> Experience:</strong><br>
                                <?php echo htmlspecialchars($user['experience'] ?: 'Not specified'); ?></p>
                            <p><strong><i class="fas fa-graduation-cap text-info"></i> Education:</strong><br>
                                <?php echo htmlspecialchars($user['education'] ?: 'Not provided'); ?></p>
                        </div>
                    </div>
                </div>

                <div class="card shadow border-0 mt-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-tools"></i> Key Skills</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($user['skills'])): ?>
                            <div class="tags">
                                <?php 
                                $skills = explode(',', $user['skills']);
                                foreach ($skills as $skill): 
                                    $skill = trim($skill);
                                    if (!empty($skill)):
                                ?>
                                    <span class="badge badge-info badge-pill mr-2 mb-2 p-2"><?php echo htmlspecialchars($skill); ?></span>
                                <?php 
                                    endif;
                                endforeach; 
                                ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No skills listed.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Applications History -->
            <div class="col-lg-8">
                <div class="card shadow border-0">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><i class="fas fa-file-alt mr-2"></i> Application History</h4>
                        <span class="badge badge-light badge-lg">
                            <?php
                            $count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM applications WHERE user_id = ?");
                            $count_stmt->bind_param("i", $user_id);
                            $count_stmt->execute();
                            $count_result = $count_stmt->get_result();
                            $app_count = $count_result->fetch_assoc()['total'];
                            echo $app_count . " Application" . ($app_count != 1 ? 's' : '');
                            $count_stmt->close();
                            ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <?php
                        $app_sql = "SELECT a.*, j.title, j.company, j.location, j.created_at AS job_posted
                                    FROM applications a
                                    JOIN jobs j ON a.job_id = j.id
                                    WHERE a.user_id = ?
                                    ORDER BY a.applied_at DESC";
                        $app_stmt = $conn->prepare($app_sql);
                        $app_stmt->bind_param("i", $user_id);
                        $app_stmt->execute();
                        $app_result = $app_stmt->get_result();

                        if ($app_result->num_rows > 0):
                        ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Job Title</th>
                                            <th>Company</th>
                                            <th>Location</th>
                                            <th>Applied On</th>
                                            <th>Resume</th>
                                            <th>Cover Letter</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($app = $app_result->fetch_assoc()): 
                                            $resume_exists = !empty($app['resume_path']) && file_exists('../' . $app['resume_path']);
                                        ?>
                                            <tr>
                                                <td>
                                                    <strong><a href="../job-details.php?id=<?php echo $app['job_id']; ?>" target="_blank">
                                                        <?php echo htmlspecialchars($app['title']); ?>
                                                    </a></strong>
                                                </td>
                                                <td><?php echo htmlspecialchars($app['company']); ?></td>
                                                <td><?php echo htmlspecialchars($app['location']); ?></td>
                                                <td><?php echo date("M j, Y", strtotime($app['applied_at'])); ?></td>
                                                <td>
                                                    <?php if ($resume_exists): ?>
                                                        <a href="../<?php echo $app['resume_path']; ?>" target="_blank" class="btn btn-success btn-sm">
                                                            <i class="fas fa-file-pdf"></i> View
                                                        </a>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($app['cover_letter'])): ?>
                                                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#coverModal<?php echo $app['id']; ?>">
                                                            <i class="fas fa-envelope"></i> View
                                                        </button>
                                                    <?php else: ?>
                                                        <span class="text-muted">—</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>

                                            <!-- Cover Letter Modal -->
                                            <?php if (!empty($app['cover_letter'])): ?>
                                            <div class="modal fade" id="coverModal<?php echo $app['id']; ?>">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title">Cover Letter for <?php echo htmlspecialchars($app['title']); ?></h5>
                                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
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

                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-file-alt fa-4x mb-3"></i>
                                <h5>No applications yet</h5>
                                <p>This jobseeker hasn't applied to any jobs.</p>
                            </div>
                        <?php endif; ?>
                        <?php $app_stmt->close(); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="manage-users.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-arrow-left"></i> Back to Jobseekers
            </a>
        </div>
    <?php endif; ?>
</div>

</div> <!-- End .container-fluid -->
</div> <!-- End .main-content -->

<?php include '../includes/footer.php'; ?>
</body>
</html>