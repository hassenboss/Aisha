<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'lab_technician' && $_SESSION['role'] != 'doctor')) {
    header("Location: login.php");
    exit();
}

include('config.php');

$test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 0;
$message = '';
$test_data = [];
$patient_data = [];
$search_results = [];
$search_performed = false;

// معالجة بحث الرقم الفريد
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['unique_id'])) {
    $unique_id = mysqli_real_escape_string($conn, $_GET['unique_id']);
    
    $query = "SELECT lt.*, p.name, p.patient_id 
              FROM lab_tests lt
              JOIN patients p ON lt.patient_id = p.patient_id
              WHERE p.unique_id = '$unique_id' 
              AND lt.completion_date IS NULL";
              
    $search_results = mysqli_query($conn, $query);
    $search_performed = true;
}

// جلب بيانات الفحص المحدد
if ($test_id > 0) {
    $query = "SELECT lt.*, p.name, p.unique_id, p.date_of_birth 
              FROM lab_tests lt
              JOIN patients p ON lt.patient_id = p.patient_id
              WHERE lt.test_id = $test_id";
    $result = mysqli_query($conn, $query);
    $test_data = mysqli_fetch_assoc($result);
    
    if ($test_data) {
        $patient_data = [
            'age' => date_diff(date_create($test_data['date_of_birth']), date_create('today'))->y,
            'gender' => $test_data['gender']
        ];
    }
}

// معالجة إرسال النتائج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $result_value = mysqli_real_escape_string($conn, $_POST['result_value']);
    $normal_range = mysqli_real_escape_string($conn, $_POST['normal_range']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $query = "UPDATE lab_tests SET
              result_value = '$result_value',
              normal_range = '$normal_range',
              status = '$status',
              notes = '$notes',
              completion_date = NOW()
              WHERE test_id = $test_id";

    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert alert-success'>تم حفظ النتائج بنجاح</div>";
        // Refresh data
        $result = mysqli_query($conn, "SELECT * FROM lab_tests WHERE test_id = $test_id");
        $test_data = mysqli_fetch_assoc($result);
    } else {
        $message = "<div class='alert alert-danger'>خطأ في الحفظ: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة نتائج الفحص</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .search-box {
            background: #e9ecef;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
        .report-header {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        .test-list {
            border: 1px solid #dee2e6;
            border-radius: 10px;
            max-height: 400px;
            overflow-y: auto;
        }
        .test-item {
            padding: 1rem;
            border-bottom: 1px solid #dee2e6;
            transition: background-color 0.3s;
        }
        .test-item:hover {
            background-color: #f8f9fa;
        }
        .result-card {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            background-color: white;
        }
        .required-star {
            color: #dc3545;
            font-size: 0.8em;
        }
        .form-label {
            font-weight: 500;
        }
        .normal-range {
            border-left: 3px solid #28a745;
            padding-left: 1rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- صندوق البحث -->
        <div class="search-box">
            <form method="GET">
                <div class="input-group">
                    <input type="text" 
                           name="unique_id" 
                           class="form-control" 
                           placeholder="ابحث بالرقم الفريد للمريض"
                           required>
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </form>
        </div>

        <?php if($search_performed): ?>
            <?php if(mysqli_num_rows($search_results) > 0): ?>
                <!-- عرض نتائج البحث -->
                <div class="test-list mb-4">
                    <h4 class="mb-3">الفحوصات المعلقة للمريض:</h4>
                    <?php while($test = mysqli_fetch_assoc($search_results)): ?>
                        <div class="test-item">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5><?= htmlspecialchars($test['test_name']) ?></h5>
                                    <p class="text-muted mb-0">
                                        تاريخ الطلب: <?= htmlspecialchars($test['request_date']) ?>
                                    </p>
                                </div>
                                <div class="col-md-6 text-start">
                                    <a href="?test_id=<?= $test['test_id'] ?>" 
                                       class="btn btn-success">
                                       <i class="fas fa-edit"></i> إضافة النتائج
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">لا توجد فحوصات معلقة لهذا المريض</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if($test_id > 0 && !empty($test_data)): ?>
            <!-- رأس التقرير -->
            <div class="report-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1><i class="fas fa-file-medical"></i> تسجيل نتائج الفحص</h1>
                        <h3><?= htmlspecialchars($test_data['test_name']) ?></h3>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-light">
                            <div class="card-body">
                                <p class="mb-1">رقم الفحص: <?= $test_data['test_id'] ?></p>
                                <p class="mb-1">تاريخ الطلب: <?= $test_data['request_date'] ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- معلومات المريض -->
            <div class="card mb-4">
                <div class="card-body">
                    <h4 class="card-title"><i class="fas fa-user-injured"></i> معلومات المريض</h4>
                    <div class="row">
                        <div class="col-md-4">
                            <p>الاسم: <?= htmlspecialchars($test_data['name']) ?></p>
                            <p>رقم الملف: <?= htmlspecialchars($test_data['unique_id']) ?></p>
                        </div>
                        <div class="col-md-4">
                            <p>العمر: <?= $patient_data['age'] ?? '--' ?> سنة</p>
                            <p>الجنس: <?= htmlspecialchars($patient_data['gender'] ?? '--') ?></p>
                        </div>
                        <div class="col-md-4">
                            <p>الأولوية: 
                                <span class="badge bg-<?= $test_data['priority'] == 'high' ? 'danger' : 'success' ?>">
                                    <?= htmlspecialchars($test_data['priority']) ?>
                                </span>
                            </p>
                            <p>الحالة الحالية: 
                                <span class="badge bg-<?= $test_data['status'] ? ($test_data['status'] == 'normal' ? 'success' : 'danger') : 'secondary' ?>">
                                    <?= $test_data['status'] ?? 'قيد الانتظار' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <?php echo $message; ?>

            <!-- نموذج إدخال النتائج -->
            <div class="card result-card">
                <div class="card-body">
                    <form method="post">
                        <div class="row g-3">
                            <!-- النتيجة -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    النتيجة <span class="required-star">*</span>
                                </label>
                                <input type="text" name="result_value" 
                                    class="form-control" 
                                    value="<?= htmlspecialchars($test_data['result_value'] ?? '') ?>"
                                    required>
                            </div>

                            <!-- المعدل الطبيعي -->
                            <div class="col-md-6">
                                <label class="form-label">
                                    المعدل الطبيعي <span class="required-star">*</span>
                                </label>
                                <input type="text" name="normal_range" 
                                    class="form-control normal-range"
                                    value="<?= htmlspecialchars($test_data['normal_range'] ?? '') ?>"
                                    required>
                            </div>

                            <!-- الحالة -->
                            <div class="col-md-4">
                                <label class="form-label">
                                    الحالة <span class="required-star">*</span>
                                </label>
                                <select name="status" class="form-select" required>
                                    <option value="normal" <?= ($test_data['status'] ?? '') == 'normal' ? 'selected' : '' ?>>طبيعي</option>
                                    <option value="abnormal" <?= ($test_data['status'] ?? '') == 'abnormal' ? 'selected' : '' ?>>غير طبيعي</option>
                                </select>
                            </div>

                            <!-- الملاحظات -->
                            <div class="col-12">
                                <label class="form-label">ملاحظات الفحص</label>
                                <textarea name="notes" 
                                    class="form-control" 
                                    rows="4"
                                    placeholder="أدخل أي ملاحظات مهمة..."><?= htmlspecialchars($test_data['notes'] ?? '') ?></textarea>
                            </div>

                            <!-- أزرار التحكم -->
                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> حفظ النتائج
                                </button>
                                <a href="lab_dashboard.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> العودة
                                </a>
                                <?php if($test_data['status']): ?>
                                <a href="test_result.php?test_id=<?= $test_id ?>" class="btn btn-success">
                                    <i class="fas fa-print"></i> عرض التقرير
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php elseif($test_id > 0): ?>
            <div class="alert alert-danger">الفحص المطلوب غير موجود</div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>