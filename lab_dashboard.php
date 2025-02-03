<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'lab_technician') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratory Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2A5C82;
            --secondary-color: #5BA4E6;
            --success-color: #28a745;
            --danger-color: #dc3545;
        }

        body {
            background-color: #f8f9fa;
            min-height: 100vh;
        }

        .dashboard-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .dashboard-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .nav-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.15);
        }

        .card-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Header Section -->
        <div class="dashboard-header text-center">
            <h1 class="mb-3">
                <i class="fas fa-microscope"></i> لوحة معلومات المختبر
            </h1>
            <p class="lead">مرحبا , <?= htmlspecialchars($_SESSION['username'] ?? 'فني مختبر') ?></p>
        </div>

        

        <!-- Quick Actions -->
        <div class="quick-actions">
            <!-- Manage Tests Card -->
            <a href="tester.php" class="text-decoration-none">
                <div class="card nav-card">
                    <div class="card-body text-center">
                        <i class="fas fa-vial card-icon"></i>
                        <h3 class="card-title">إدارة الفحوصات</h3>
                        <p class="text-muted">إنشاء وتحديث الفحوصات المعملية</p>
                    </div>
                </div>
            </a>

            <!-- Test Results Card -->
            <a href="Test_result.php" class="text-decoration-none">
                <div class="card nav-card">
                    <div class="card-body text-center">
                        <i class="fas fa-file-medical card-icon"></i>
                        <h3 class="card-title">نتائج الفحوصات</h3>
                        <p class="text-muted">إدخال نتائج الفحوصات وإدارتها</p>
                    </div>
                </div>
            </a>

            <!-- Reports Card -->
            <a href="test_report.php" class="text-decoration-none">
                <div class="card nav-card">
                    <div class="card-body text-center">
                        <i class="fas fa-chart-bar card-icon"></i>
                        <h3 class="card-title">التحليلات والتقارير</h3>
                        <p class="text-muted">عرض تقارير مفصلة</p>
                    </div>
                </div>
            </a>
        </div>

       

        <!-- Logout Button -->
        <div class="text-center mt-4">
            <a href="logout.php" class="btn btn-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>