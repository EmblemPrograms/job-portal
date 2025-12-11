<?php include 'includes/user-navbar.php'; ?>

<?php
// Redirect if not logged in
if (!$logged_in) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Applications - JobPortal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body { background: #f8f9fa; }
        .page-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }
        .application-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .application-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }
        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 50px;
        }
        .no-applications {
            text-align: center;
            padding: 80px 20px;
            color: #6c757d;
        }
        .job-title-link {
            color: #007bff;
            font-weight: bold;
            font-size: 1.2rem;
        }
        .job-title-link:hover {
            color: #0056b3;
            text-decoration: underline;
        }
    </style>
</head>
<body>

<!-- Page Header -->
<div class="page-header text-center">
    <div class="container">
        <h1 class="display-4 font-weight-bold"><i class="fas fa-file-alt mr-3"></i> My Applications</h1>
        <p class="lead">Track all the jobs you've applied to</p>
    </div>
</div>

<div class="container mb-5">
    <?php
    $sql = "SELECT 
                a.id AS app_id,
                a.resume_path,
                a.cover_letter,
                a.applied_at,
                j.id AS job_id,
                j.title,
                j.company,
                j.location,
                j.type,
                j.salary
            FROM applications a
            JOIN jobs j ON a.job_id = j.id
            WHERE a.user_id = ?
            ORDER BY a.applied_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <?php if ($result->num_rows > 0): ?>
        <div class="row">
            <?php while ($app = $result->fetch_assoc()): 
                $resume_exists = !empty($app['resume_path']) && file_exists($app['resume_path']);
            ?>
                <div class="col-lg-6 mb-4">
                    <div class="card application-card h-100">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <a href="job-details.php?id=<?php echo $app['job_id']; ?>" class="job-title-link">
                                        <?php echo htmlspecialchars($app['title']); ?>
                                    </a>
                                    <p class="mb-1"><strong><?php echo htmlspecialchars($app['company']); ?></strong></p>
                                    <p class="text-muted small">
                                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($app['location']); ?>
                                    </p>
                                </div>
                                <span class="status-badge bg-success text-white">
                                    <i class="fas fa-check-circle"></i> Applied
                                </span>
                            </div>

                            <div class="row small text-muted">
                                <div class="col-md-6">
                                    <strong>Type:</strong> <?php echo htmlspecialchars($app['type'] ?? 'Full-time'); ?><br>
                                    <strong>Salary:</strong> <?php echo htmlspecialchars($app['salary'] ?? 'Not disclosed'); ?>
                                </div>
                                <div class="col-md-6">
                                    <strong>Applied on:</strong><br>
                                    <?php echo date("M j, Y", strtotime($app['applied_at'])); ?>
                                </div>
                            </div>

                            <hr>

                            <div class="mt-3">
                                <?php if ($resume_exists): ?>
                                    <a href="<?php echo $app['resume_path']; ?>" target="_blank" class="btn btn-outline-success btn-sm mr-2">
                                        <i class="fas fa-file-pdf"></i> View Resume
                                    </a>
                                <?php endif; ?>

                                <?php if (!empty($app['cover_letter'])): ?>
                                    <button class="btn btn-outline-info btn-sm mr-2" data-toggle="modal" data-target="#coverModal<?php echo $app['app_id']; ?>">
                                        <i class="fas fa-envelope"></i> View Cover Letter
                                    </button>
                                <?php endif; ?>

                                <a href="job-details.php?id=<?php echo $app['job_id']; ?>" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-external-link-alt"></i> View Job
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cover Letter Modal -->
                <?php if (!empty($app['cover_letter'])): ?>
                <div class="modal fade" id="coverModal<?php echo $app['app_id']; ?>">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title">Your Cover Letter</h5>
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
        </div>
    <?php else: ?>
        <div class="no-applications">
            <i class="fas fa-file-alt fa-5x mb-4 text-muted"></i>
            <h3>No Applications Yet</h3>
            <p>You haven't applied to any jobs.</p>
            <a href="jobs.php" class="btn btn-primary btn-lg">
                <i class="fas fa-search mr-2"></i> Browse Jobs
            </a>
        </div>
    <?php endif; ?>

    <?php $stmt->close(); ?>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>