<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'lab_technician' && $_SESSION['role'] != 'doctor')) {
    header("Location: login.php");
    exit();
}

include('config.php');
// جلب معاملات البحث
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$unique_id = isset($_GET['unique_id']) ? mysqli_real_escape_string($conn, $_GET['unique_id']) : '';
$test_type = isset($_GET['test_type']) ? mysqli_real_escape_string($conn, $_GET['test_type']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// بناء الاستعلام مع الفلترة
$query = "SELECT lt.*, p.name AS patient_name, p.unique_id 
          FROM lab_tests lt
          JOIN patients p ON lt.patient_id = p.patient_id
          WHERE lt.completion_date IS NOT NULL";

if (!empty($search)) {
    $query .= " AND p.name LIKE '%$search%'";
}

if (!empty($unique_id)) {
    $query .= " AND p.unique_id = '$unique_id'";
}

if (!empty($test_type)) {
    $query .= " AND lt.test_type = '$test_type'";
}

if (!empty($status)) {
    $query .= " AND lt.status = '$status'";
}

$query .= " ORDER BY lt.completion_date DESC";

$tests = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقارير ونتائج الفحوصات</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .report-header {
            background: linear-gradient(135deg, #2A5C82, #5BA4E6);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        .filter-box {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .test-card {
            border-left: 4px solid #2A5C82;
            border-radius: 8px;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }
        .test-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }
        .badge-status {
            font-size: 0.9em;
            padding: 0.5em 0.8em;
        }
        .print-btn {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- رأس الصفحة -->
        <div class="report-header">
            <h1><i class="fas fa-file-medical"></i> تقارير ونتائج الفحوصات</h1>
            <p class="lead">عرض شامل لجميع الفحوصات المكتملة</p>
        </div>

        <!-- مربع الفلترة -->
        <div class="filter-box">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>بحث بالاسم:</label>
                        <input type="text" name="search" class="form-control" placeholder="اسم المريض">
                    </div>
                     <!-- بحث بالرقم الفريد -->
                     <div class="col-md-4">
                        <label>بحث بالرقم الفريد:</label>
                        <input type="text" name="unique_id" class="form-control" 
                               placeholder="الرقم الفريد" value="<?= htmlspecialchars($unique_id) ?>">
                    </div>
                    <div class="col-md-4">
                        <label>نوع الفحص:</label>
                        <select name="test_type" class="form-select">
                            <option value="">الكل</option>
                            <option value="blood">فحص دم</option>
                            <option value="urine">فحص بول</option>
                            <option value="xray">أشعة</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>الحالة:</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="normal">طبيعي</option>
                            <option value="abnormal">غير طبيعي</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter"></i> تطبيق الفلترة
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- قائمة الفحوصات -->
        <div class="row">
            <?php while($test = mysqli_fetch_assoc($tests)): ?>
            <div class="col-md-6">
                <div class="card test-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title">
                                    <?= htmlspecialchars($test['test_name']) ?>
                                    <span class="badge badge-status bg-<?= $test['status'] == 'normal' ? 'success' : 'danger' ?>">
                                        <?= htmlspecialchars($test['status']) ?>
                                    </span>
                                </h5>
                                <p class="text-muted mb-1">
                                    <i class="fas fa-user"></i> 
                                    <?= htmlspecialchars($test['patient_name']) ?>
                                </p>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-id-badge"></i>
                                    <?= htmlspecialchars($test['unique_id']) ?>
                                </p>
                            </div>
                            <small class="text-muted">
                                <?= htmlspecialchars($test['completion_date']) ?>
                            </small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>النتيجة:</strong> <?= htmlspecialchars($test['result_value']) ?></p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>المعدل الطبيعي:</strong> <?= htmlspecialchars($test['normal_range']) ?></p>
                            </div>
                        </div>
                        
                        <?php if(!empty($test['comments'])): ?>
                            <div class="alert alert-info mt-2">
                                <strong>ملاحظات:</strong> <?= nl2br(htmlspecialchars($test['comments'])) ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mt-3">
                            <a href="test_report.php?id=<?= $test['test_id'] ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> عرض التفاصيل
                            </a>
                            <a href="test_report.php?id=<?= $test['test_id'] ?>&print=true" class="btn btn-sm btn-success">
                                <i class="fas fa-print"></i> طباعة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- زر الطباعة -->
        <button onclick="window.print()" class="btn btn-primary print-btn">
            <i class="fas fa-print"></i> طباعة الكل
        </button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>