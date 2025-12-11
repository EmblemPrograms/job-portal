<?php include 'includes/user-navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Jobs - JobPortal</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .job-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 20px;
        }
        .job-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        .job-title {
            color: #007bff;
            font-weight: bold;
            font-size: 1.3rem;
        }
        .company-name {
            font-weight: 600;
            color: #333;
        }
        .badge-location {
            background: #e9ecef;
            color: #495057;
            font-weight: 500;
        }
        .search-box {
            border-radius: 50px;
            padding: 12px 25px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .no-jobs {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
    </style>
</head>
<body class="bg-light">

<div class="container mt-4">

    <!-- Page Title + Search -->
    <div class="row mb-4">
        <div class="col-12 text-center">
            <h1 class="display-4 font-weight-bold text-primary">
                <i class="fas fa-briefcase"></i> Browse All Jobs
            </h1>
            <p class="lead">Find your dream job from hundreds of opportunities</p>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="row mb-5">
        <div class="col-lg-8 mx-auto">
            <form method="GET" action="jobs.php">
                <div class="input-group input-group-lg">
                    <input type="text" name="search" class="form-control search-box" 
                           placeholder="Search by job title, company, or skills..." 
                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Jobs Grid -->
    <div class="row">
        <?php
        // Search functionality
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        $sql = "SELECT * FROM jobs WHERE 1=1";
        if (!empty($search)) {
            $searchTerm = "%$search%";
            $sql .= " AND (title LIKE ? OR company LIKE ? OR location LIKE ? OR description LIKE ?)";
        }
        $sql .= " ORDER BY created_at DESC";

        $stmt = $conn->prepare($sql);
        if (!empty($search)) {
            $stmt->bind_param("ssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm);
        }
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0):
            while ($job = $result->fetch_assoc()):
        ?>
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card job-card h-100">
                        <div class="card-body p-4">
                            <h5 class="job-title"><?php echo htmlspecialchars($job['title']); ?></h5>
                            <p class="company-name mb-2">
                                <i class="fas fa-building text-muted"></i> <?php echo htmlspecialchars($job['company']); ?>
                            </p>
                            <p class="mb-2">
                                <span class="badge badge-location">
                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?>
                                </span>
                            </p>
                            <p class="text-muted small">
                                <?php echo substr(htmlspecialchars($job['description']), 0, 120); ?>...
                            </p>
                            <div class="text-right">
                                <a href="job-details.php?id=<?php echo $job['id']; ?>" 
                                   class="btn btn-outline-primary btn-sm px-4">
                                    View Details <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                        <div class="card-footer bg-white text-muted small">
                            Posted on: <?php echo date("M j, Y", strtotime($job['created_at'])); ?>
                        </div>
                    </div>
                </div>
        <?php
            endwhile;
        else:
        ?>
            <div class="col-12">
                <div class="no-jobs">
                    <i class="fas fa-search fa-5x text-muted mb-4"></i>
                    <h3>No jobs found</h3>
                    <p>
                        <?php echo !empty($search) ? "No jobs match '<strong>$search</strong>'." : "No jobs posted yet."; ?>
                        <br><a href="jobs.php" class="btn btn-primary mt-3">View All Jobs</a>
                    </p>
                </div>
            </div>
        <?php
        endif;
        $stmt->close();
        ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>