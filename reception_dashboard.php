<?php
session_start();
if ($_SESSION['role'] != 'receptionist') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reception Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .dashboard-header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card-icon {
            font-size: 2.5rem;
            color: #007bff;
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-top: 10px;
        }
        .card-link {
            text-decoration: none;
            color: inherit;
        }
        .card-link:hover {
            text-decoration: none;
        }
        .logout-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <h1>مرحبًا، موظف الاستقبال</h1>
        <p>قم بإدارة مهامك بكفاءة</p>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="row g-4">
            <!-- Manage Patients Card -->
            <div class="col-md-6 col-lg-4">
                <a href="manage_patients.php" class="card-link">
                    <div class="card text-center p-4">
                        <div class="card-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="card-title">إدارة المرضى</div>
                        <p class="text-muted">أضف،أو عدل أو احذف سجلات المرضى</p>
                    </div>
                </a>
            </div>

            <!-- Manage Appointments Card -->
            <div class="col-md-6 col-lg-4">
                <a href="manage_appointments.php" class="card-link">
                    <div class="card text-center p-4">
                        <div class="card-icon">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="card-title">إدارة المواعيد</div>
                        <p class="text-muted">أضافة أو تحديث المواعيد</p>
                    </div>
                </a>
            </div>

            <!-- View Reports Card -->
            <div class="col-md-6 col-lg-4">
                <a href="reports.php" class="card-link">
                    <div class="card text-center p-4">
                        <div class="card-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="card-title">عرض التقارير</div>
                        <p class="text-muted">توليد وتحليل التقارير</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Notification</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            You have 3 new appointments today!
        </div>
    </div>
</div>

    <!-- Logout Button -->
    <a href="logout.php" class="btn btn-danger logout-btn">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>