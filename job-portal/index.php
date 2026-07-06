<?php
// Start session and include connection
session_start();
include 'includes/connect.php';
include 'includes/functions.php';

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
    <section class="bg-gradient-primary text-white py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-center text-lg-left mb-4 mb-lg-0">
                    <span class="badge badge-light text-primary mb-3 px-3 py-2" style="font-size:.8rem;">
                        <i class="fas fa-bolt mr-1"></i> Over 10,000+ opportunities
                    </span>
                    <h1 class="display-4 font-weight-bold">Find Your Dream Job Today</h1>
                    <p class="lead mb-4">Thousands of jobs in tech, marketing, design, finance and more — all in one place.</p>

                    <!-- Search Form -->
                    <form action="jobs.php" method="GET" class="bg-white p-2 rounded shadow-lg">
                        <div class="form-row align-items-center">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <input type="text" name="search" class="form-control form-control-lg border-0" placeholder="Job title, keywords or company..." required>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <input type="text" name="location" class="form-control form-control-lg border-0" placeholder="Location">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                    <p class="small mt-3 mb-0" style="opacity:.85;">
                        <i class="fas fa-fire mr-1"></i> Popular: Developer, Designer, Marketing, Finance
                    </p>
                </div>
                <div class="col-lg-6 text-center">
                    <img src="assets/images/hero.svg" alt="Job search illustration" class="hero-illustration">
                </div>
            </div>
        </div>
    </section>

    <!-- Browse by Category -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-1">Browse by Category</h2>
            <p class="text-center text-muted mb-4">Explore roles across the fields you love</p>
            <div class="row">
                <?php
                $cats = [
                    ['Engineering', 'fa-code',       'Developer'],
                    ['Design',      'fa-pen-nib',    'Designer'],
                    ['Marketing',   'fa-bullhorn',   'Marketing'],
                    ['Finance',     'fa-chart-line', 'Analyst'],
                    ['DevOps',      'fa-server',     'DevOps'],
                    ['Support',     'fa-headset',    'Support'],
                ];
                foreach ($cats as $c) {
                    $cat = job_category($c[2]);
                    $grad = "linear-gradient(135deg, {$cat['grad'][0]} 0%, {$cat['grad'][1]} 100%)";
                    echo '
                    <div class="col-6 col-md-4 col-lg-2 mb-4">
                        <a href="jobs.php?search=' . urlencode($c[2]) . '" class="category-tile">
                            <div class="cat-icon" style="background:' . $grad . '">
                                <i class="fas ' . $c[1] . '"></i>
                            </div>
                            <div class="font-weight-bold">' . $c[0] . '</div>
                        </a>
                    </div>';
                }
                ?>
            </div>
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
                            <div class="card h-100 shadow-sm hover-shadow">
                                ' . job_banner_html($job['title'], $job['company']) . '
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