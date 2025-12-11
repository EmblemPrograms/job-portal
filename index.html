<?php
// Start session and include connection
session_start();
include 'includes/connect.php';

// Include the dynamic user navbar (handles both logged-in and guest states)
include 'includes/user-navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JobPortal - Find Your Dream Job</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hover-shadow {
            transition: all 0.3s ease;
        }
        .hover-shadow:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
        }
    </style>
</head>
<body>

    <!-- Hero Section with Search -->
    <section class="bg-primary text-white py-5">
        <div class="container text-center">
            <h1 class="display-4 font-weight-bold">Find Your Dream Job Today</h1>
            <p class="lead mb-4">Thousands of jobs in tech, marketing, design, and more.</p>

            <!-- Search Form -->
            <form action="jobs.php" method="GET" class="row justify-content-center">
                <div class="col-md-4 mb-3">
                    <input type="text" name="keywords" class="form-control form-control-lg" placeholder="Job title, keywords..." required>
                </div>
                <div class="col-md-3 mb-3">
                    <input type="text" name="location" class="form-control form-control-lg" placeholder="Location (e.g. Remote, New York)">
                </div>
                <div class="col-md-3 mb-3">
                    <button type="submit" class="btn btn-light btn-lg btn-block">
                        <i class="fas fa-search"></i> Search Jobs
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Featured Jobs Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-4">Latest Job Openings</h2>
            <div class="row">

                <?php
                // Fetch latest 6 jobs from database
                $sql = "SELECT * FROM jobs ORDER BY created_at DESC LIMIT 6";
                $result = $conn->query($sql);

                if ($result && $result->num_rows > 0) {
                    while($job = $result->fetch_assoc()) {
                        echo '
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shadow-sm hover-shadow border-0">
                                <div class="card-body">
                                    <h5 class="card-title text-primary font-weight-bold">' . htmlspecialchars($job['title']) . '</h5>
                                    <p class="card-text">
                                        <strong>Company:</strong> ' . htmlspecialchars($job['company']) . '<br>
                                        <strong>Location:</strong> ' . htmlspecialchars($job['location']) . '
                                    </p>
                                    <p class="text-muted small">' . substr(htmlspecialchars($job['description']), 0, 120) . '...</p>
                                </div>
                                <div class="card-footer bg-transparent border-0 text-right">
                                    <a href="job-details.php?id=' . $job['id'] . '" class="btn btn-outline-primary btn-sm">
                                        View Details <i class="fas fa-arrow-right ml-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>';
                    }
                } else {
                    echo '<div class="col-12 text-center py-5">
                            <p class="lead text-muted">No jobs available yet. Check back soon!</p>
                          </div>';
                }
                ?>

            </div>
            <div class="text-center mt-4">
                <a href="jobs.php" class="btn btn-primary btn-lg px-5">
                    <i class="fas fa-briefcase mr-2"></i> View All Jobs
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-4 mt-5">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date("Y"); ?> JobPortal. All rights reserved.</p>
            <small>Made with <i class="fas fa-heart text-danger"></i> for Jobseekers</small>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>