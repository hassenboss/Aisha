<?php
session_start();
if ($_SESSION['role'] != 'receptionist') {
    header("Location: login.php");
    exit();
}

include('config.php');

// جلب إحصائيات المرضى
$total_patients = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM patients"))['total'];

// جلب إحصائيات المواعيد
$total_appointments = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM appointments"))['total'];

// جلب إحصائيات حسب الجنس
$gender_stats = mysqli_query($conn, "SELECT gender, COUNT(*) as count FROM patients GROUP BY gender");

// جلب إحصائيات المواعيد حسب الشهر
$monthly_stats = mysqli_query($conn, "
    SELECT DATE_FORMAT(date, '%Y-%m') as month, COUNT(*) as count 
    FROM appointments 
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    ORDER BY month DESC
");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
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
        .table {
            margin-top: 20px;
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">التقارير والإحصائيات</h1>

        <!-- إحصائيات سريعة -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card text-center p-4">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-title">إجمالي المرضى</div>
                    <div class="display-4"><?= $total_patients ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center p-4">
                    <div class="card-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="card-title">إجمالي المواعيد</div>
                    <div class="display-4"><?= $total_appointments ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-center p-4">
                    <div class="card-icon">
                        <i class="fas fa-chart-pie"></i>
                    </div>
                    <div class="card-title">تقارير مفصلة</div>
                    <p>انظر أدناه</p>
                </div>
            </div>
        </div>

        <!-- إحصائيات حسب الجنس -->
        <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">توزيع المرضى حسب الجنس</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>الجنس</th>
                            <th>عدد المرضى</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($gender_stats)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['gender']) ?></td>
                            <td><?= htmlspecialchars($row['count']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- إحصائيات المواعيد حسب الشهر -->
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="card-title mb-0">المواعيد حسب الشهر</h5>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>الشهر</th>
                            <th>عدد المواعيد</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($monthly_stats)): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['month']) ?></td>
                            <td><?= htmlspecialchars($row['count']) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>