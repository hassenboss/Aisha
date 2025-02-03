<?php
session_start();
if ($_SESSION['role'] != 'doctor') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .dashboard-header {
            background-color: #28a745;
            color: white;
            padding: 2rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
        }
        .card-icon {
            font-size: 2.5rem;
            color: #28a745;
            margin-bottom: 1rem;
        }
        .feature-card {
            height: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .logout-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            z-index: 1000;
            border-radius: 50px;
            padding: 12px 25px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="dashboard-header">
        <h1>Welcome, Dr. <?= htmlspecialchars($_SESSION['username'] ?? '') ?></h1>
        <p class="lead">Medical Management Dashboard</p>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="row g-4">
            <!-- Patients Card -->
            <div class="col-md-6 col-lg-4">
                <a href="tester.php" class="text-decoration-none">
                    <div class="card feature-card">
                        <div class="card-body">
                            <i class="fas fa-calendar-check card-icon"></i>
                            <h3 class="card-title">إضافة فحص</h3>
                            <p class="text-muted">إضافة فحص معمل جديد</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Appointments Card -->
            <div class="col-md-6 col-lg-4">
                <a href="appointments.php" class="text-decoration-none">
                    <div class="card feature-card">
                        <div class="card-body">
                            <i class="fas fa-user-injured card-icon"></i>
                            <h3 class="card-title">تشخيص الحالة</h3>
                            <p class="text-muted">تشخيص المرض وتعديل الحالة المرضية</p>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Medical History Card -->
            <div class="col-md-6 col-lg-4">
                <a href="test_report.php" class="text-decoration-none">
                    <div class="card feature-card">
                        <div class="card-body">
                            <i class="fas fa-file-medical card-icon"></i>
                            <h3 class="card-title">تقارير ونتائج الفحوصات</h3>
                            <p class="text-muted">عرض شامل لجميع الفحوصات المكتملة</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Logout Button -->
    <a href="logout.php" class="btn btn-danger logout-btn" 
       onclick="return confirm('Are you sure you want to logout?')">
       <i class="fas fa-sign-out-alt"></i> Logout
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>