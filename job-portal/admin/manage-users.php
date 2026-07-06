<?php include '../includes/admin-navbar.php'; ?>

<?php
// Delete user (with confirmation)
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    
    // Prevent deleting self or last admin
    if ($delete_id == $_SESSION['user_id']) {
        $msg = "You cannot delete yourself!";
        $type = "danger";
    } else {
        $stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['role'] === 'admin') {
                // Count total admins
                $count = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'admin'")->fetch_assoc()['total'];
                if ($count <= 1) {
                    $msg = "Cannot delete the last admin account!";
                    $type = "danger";
                } else {
                    $go_delete = true;
                }
            } else {
                $go_delete = true;
            }
        }
        $stmt->close();

        if (isset($go_delete)) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $delete_id);
            if ($stmt->execute()) {
                $msg = "Jobseeker deleted successfully!";
                $type = "success";
            } else {
                $msg = "Error deleting user.";
                $type = "danger";
            }
            $stmt->close();
        }
    }
}
?>

<div class="card shadow mb-4">
    <div class="card-header py-3 bg-info text-white">
        <h4 class="m-0 font-weight-bold">
            <i class="fas fa-users-cog mr-2"></i> Manage Jobseekers
        </h4>
    </div>
    <div class="card-body">

        <?php if (isset($msg)): ?>
            <div class="alert alert-<?php echo $type; ?> alert-dismissible fade show">
                <strong><?php echo $msg; ?></strong>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="usersTable">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Full Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Experience</th>
                        <th>Skills</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC");
                    if ($result->num_rows > 0):
                        $count = 1;
                        while ($user = $result->fetch_assoc()):
                    ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($user['fullname'] ?: '—'); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['phone'] ?: '—'); ?></td>
                            <td><?php echo htmlspecialchars($user['location'] ?: '—'); ?></td>
                            <td>
                                <span class="badge badge-primary">
                                    <?php echo htmlspecialchars($user['experience'] ?: 'Not specified'); ?>
                                </span>
                            </td>
                            <td>
                                <small><?php echo substr(htmlspecialchars($user['skills'] ?: '—'), 0, 40); ?>
                                    <?php echo strlen($user['skills'] ?? '') > 40 ? '...' : ''; ?>
                                </small>
                            </td>
                            <td><?php echo date("M j, Y", strtotime($user['created_at'])); ?></td>
                            <td>
                                <a href="view-user.php?id=<?php echo $user['id']; ?>" 
                                   class="btn btn-info btn-sm" title="View Profile">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="?delete=<?php echo $user['id']; ?>" 
                                   class="btn btn-danger btn-sm" title="Delete User"
                                   onclick="return confirm('Delete this jobseeker?\nThis cannot be undone!')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fas fa-users fa-4x mb-3"></i><br>
                                <h5>No jobseekers registered yet</h5>
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

<!-- Optional: Add DataTables for search/sort (uncomment if you want) -->
<!--
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function() {
        $('#usersTable').DataTable();
    });
</script>
-->

<?php include '../includes/footer.php'; ?>
</body>
</html>