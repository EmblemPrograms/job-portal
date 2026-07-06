<?php include '../includes/admin-navbar.php'; ?>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-warning text-dark d-flex justify-content-between align-items-center">
        <h4 class="m-0 font-weight-bold">
            <i class="fas fa-file-alt mr-2"></i> All Job Applications
        </h4>
        <span class="badge badge-primary badge-lg">
            Total:
            <?php
            $total_result = $conn->query("SELECT COUNT(*) AS cnt FROM applications");
            $total = $total_result->fetch_assoc()['cnt'];
            echo $total;
            ?>
        </span>
    </div>

    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="applicationsTable">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Applicant</th>
                        <th>Contact</th>
                        <th>Job Title</th>
                        <th>Company</th>
                        <th>Applied On</th>
                        <th>Status</th>
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
            a.status,
            j.id AS job_id,
            j.title AS job_title,
            j.company,
            u.id AS user_id,
            u.fullname,
            u.username,
            u.email,
            u.phone
        FROM applications a
        JOIN jobs j ON a.job_id = j.id
        JOIN users u ON a.user_id = u.id
        ORDER BY a.applied_at DESC";

                    $result = $conn->query($sql);

                    if ($result && $result->num_rows > 0):
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
                                <td>
                                    <a href="../job-details.php?id=<?php echo $app['job_id']; ?>" target="_blank"
                                        class="text-primary">
                                        <?php echo htmlspecialchars($app['job_title']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars($app['company']); ?></td>
                                <td><?php echo date("M j, Y", strtotime($app['applied_at'])); ?></td>

                                <!-- Status Column -->
                                <td>
                                    <?php
                                    $status = $app['status'] ?? 'pending';
                                    if ($status === 'approved') {
                                        echo '<span class="badge badge-success">Approved</span>';
                                    } elseif ($status === 'rejected') {
                                        echo '<span class="badge badge-danger">Rejected</span>';
                                    } else {
                                        echo '<span class="badge badge-warning">Pending</span>';
                                    }
                                    ?>
                                </td>

                                <td>
                                    <?php if ($resume_exists): ?>
                                        <a href="../<?php echo $app['resume_path']; ?>" target="_blank"
                                            class="btn btn-success btn-sm">
                                            <i class="fas fa-file-pdf"></i> View
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">No resume</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if (!empty($app['cover_letter'])): ?>
                                        <button class="btn btn-info btn-sm" data-toggle="modal"
                                            data-target="#coverModal<?php echo $app['app_id']; ?>">
                                            <i class="fas fa-envelope-open-text"></i> View
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small">None</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Actions Column -->
                                <td class="text-nowrap">
    <a href="view-user.php?id=<?php echo $app['user_id']; ?>" 
       class="btn btn-primary btn-sm me-1" 
       title="View Profile">
        <i class="fas fa-user"></i> Profile
    </a>

    <?php if ($status === 'pending'): ?>
        <form action="process_application.php" method="post" style="display:inline;">
            <input type="hidden" name="app_id" value="<?php echo $app['app_id']; ?>">
            <input type="hidden" name="action" value="approve">
            <button type="submit" class="btn btn-outline-success btn-sm me-1"
                    onclick="return confirm('Approve this application?');">
                <i class="fas fa-check"></i> Approve
            </button>
        </form>

        <form action="process_application.php" method="post" style="display:inline;">
            <input type="hidden" name="app_id" value="<?php echo $app['app_id']; ?>">
            <input type="hidden" name="action" value="reject">
            <button type="submit" class="btn btn-outline-danger btn-sm"
                    onclick="return confirm('Reject this application?');">
                <i class="fas fa-times"></i> Reject
            </button>
        </form>

    <?php elseif ($status === 'approved'): ?>
        <span class="badge bg-success px-3 py-2">Approved</span>

    <?php elseif ($status === 'rejected'): ?>
        <span class="badge bg-danger px-3 py-2">Rejected</span>

    <?php endif; ?>
</td>
                            </tr>

                            <!-- Cover Letter Modal -->
                            <?php if (!empty($app['cover_letter'])): ?>
                                <div class="modal fade" id="coverModal<?php echo $app['app_id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-info text-white">
                                                <h5 class="modal-title">
                                                    Cover Letter -
                                                    <?php echo htmlspecialchars($app['fullname'] ?: $app['username']); ?>
                                                </h5>
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

                            <?php
                        endwhile;
                    else:
                        ?>
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-file-alt fa-5x mb-4"></i><br>
                                <h4>No applications received yet</h4>
                                <p>Once jobseekers start applying, their submissions will appear here.</p>
                                <a href="manage-jobs.php" class="btn btn-primary">View Jobs</a>
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