<?php include '../includes/admin-navbar.php'; ?>

<div class="row">
    <!-- Stats Cards -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Jobs
, Jobs</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as total FROM jobs");
                            echo $result->fetch_assoc()['total'];
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-briefcase fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Jobseekers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'user'");
                            echo $result->fetch_assoc()['total'];
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col(md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Applications</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                            $result = $conn->query("SELECT COUNT(*) as total FROM applications");
                            echo $result->fetch_assoc()['total'];
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-file-alt fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Pending Applications</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?php
                            // You can add status column later. For now, show all
                            $result = $conn->query("SELECT COUNT(*) as total FROM applications");
                            echo $result->fetch_assoc()['total'];
                            ?>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Jobs Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 bg-primary text-white">
        <h5 class="m-0 font-weight-bold">
            <i class="fas fa-briefcase mr-2"></i> Recently Posted Jobs
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                <thead class="thead-light">
                    <tr>
                        <th>Title</th>
                        <th>Company</th>
                        <th>Location</th>
                        <th>Posted On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM jobs ORDER BY created_at DESC LIMIT 10");
                    if ($result->num_rows > 0):
                        while ($job = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($job['title']); ?></strong></td>
                            <td><?php echo htmlspecialchars($job['company']); ?></td>
                            <td><span class="badge badge-info"><?php echo htmlspecialchars($job['location']); ?></span></td>
                            <td><?php echo date("M j, Y", strtotime($job['created_at'])); ?></td>
                            <td>
                                <a href="edit-job.php?id=<?php echo $job['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="delete-job.php?id=<?php echo $job['id']; ?>" class="btn btn-danger btn-sm" 
                                   onclick="return confirm('Delete this job?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-briefcase fa-3x mb-3"></i><br>
                                No jobs posted yet. <a href="add-job.php">Add your first job!</a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-6 mb-4">
        <a href="add-job.php" class="btn btn-success btn-lg btn-block shadow">
            <i class="fas fa-plus-circle fa-2x mb-2 d-block"></i>
            <strong>Add New Job</strong>
        </a>
    </div>
    <div class="col-md-6 mb-4">
        <a href="manage-users.php" class="btn btn-info btn-lg btn-block shadow text-white">
            <i class="fas fa-users fa-2x mb-2 d-block"></i>
            <strong>Manage Jobseekers</strong>
        </a>
    </div>
</div>

</div> <!-- End of .container-fluid -->
</div> <!-- End of .main-content -->

<?php include '../includes/footer.php'; ?>
</body>
</html>