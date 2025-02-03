<?php
session_start();
if ($_SESSION['role'] != 'doctor') {
    header("Location: login.php");
    exit();
}

include('config.php');

$test_id = isset($_GET['test_id']) ? intval($_GET['test_id']) : 2;
$message = '';
$test_data = [];
$patient_data = [];

// جلب بيانات الفحص
if ($test_id > 0) {
    $query = "SELECT lt.*, p.name AS patient_name, p.unique_id, p.date_of_birth, p.medical_history
              FROM lab_tests lt
              JOIN patients p ON lt.patient_id = p.patient_id
              WHERE lt.test_id = $test_id";
    $result = mysqli_query($conn, $query);
    $test_data = mysqli_fetch_assoc($result);
    
    if ($test_data) {
        $patient_data = [
            'age' => date_diff(date_create($test_data['date_of_birth']), date_create('today'))->y,
           
        ];
    }
}

// معالجة إرسال التشخيص
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diagnosis = mysqli_real_escape_string($conn, $_POST['diagnosis']);
    $treatment_plan = mysqli_real_escape_string($conn, $_POST['treatment_plan']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $query = "UPDATE lab_tests SET
              diagnosis = '$diagnosis',
              treatment_plan = '$treatment_plan',
              doctor_notes = '$notes',
              diagnosis_date = NOW()
              WHERE test_id = $test_id";

    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert alert-success'>تم حفظ التشخيص بنجاح</div>";
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
    <title>تشخيص حالة المريض</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .diagnosis-header {
            background: linear-gradient(135deg, #2A5C82, #5BA4E6);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        .patient-info {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .test-results {
            border-left: 4px solid #2A5C82;
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        .diagnosis-form {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .required-star {
            color: #dc3545;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <?php if(empty($test_data)): ?>
            <div class="alert alert-danger">الفحص المطلوب غير موجود</div>
        <?php else: ?>
            <!-- رأس الصفحة -->
            <div class="diagnosis-header">
                <h1><i class="fas fa-stethoscope"></i> تشخيص حالة المريض</h1>
                <h3>بناءً على نتائج الفحص المخبري</h3>
            </div>

            <?php echo $message; ?>

            <!-- معلومات المريض -->
            <div class="patient-info">
                <h4><i class="fas fa-user-injured"></i> معلومات المريض</h4>
                <div class="row">
                    <div class="col-md-4">
                        <p>الاسم: <?= htmlspecialchars($test_data['patient_name']) ?></p>
                        <p>العمر: <?= $patient_data['age'] ?> سنة</p>
                    </div>
                    <div class="col-md-4">
                        <p>رقم الملف: <?= htmlspecialchars($test_data['unique_id']) ?></p>
                     
                    </div>
                    <div class="col-md-4">
                        <p>تاريخ الميلاد: <?= htmlspecialchars($test_data['date_of_birth']) ?></p>
                        <p>التاريخ المرضي: <?= nl2br(htmlspecialchars($test_data['medical_history'])) ?></p>
                    </div>
                </div>
            </div>

            <!-- نتائج الفحص -->
            <div class="test-results">
                <h4><i class="fas fa-flask"></i> نتائج الفحص</h4>
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>اسم الفحص:</strong> <?= htmlspecialchars($test_data['test_name']) ?></p>
                        <p><strong>النتيجة:</strong> <?= htmlspecialchars($test_data['result_value']) ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>المعدل الطبيعي:</strong> <?= htmlspecialchars($test_data['normal_range']) ?></p>
                        <p><strong>الحالة:</strong> 
                            <span class="badge bg-<?= $test_data['status'] == 'normal' ? 'success' : 'danger' ?>">
                                <?= htmlspecialchars($test_data['status']) ?>
                            </span>
                        </p>
                    </div>
                </div>
                <?php if(!empty($test_data['comments'])): ?>
                    <div class="alert alert-info mt-2">
                        <strong>ملاحظات المختبر:</strong> <?= nl2br(htmlspecialchars($test_data['comments'])) ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- نموذج التشخيص -->
            <div class="diagnosis-form">
                <form method="post">
                    <div class="row g-3">
                        <!-- التشخيص -->
                        <div class="col-12">
                            <label class="form-label">
                                التشخيص <span class="required-star">*</span>
                            </label>
                            <textarea name="diagnosis" class="form-control" rows="3" required
                                placeholder="أدخل التشخيص الطبي..."><?= htmlspecialchars($test_data['diagnosis'] ?? '') ?></textarea>
                        </div>

                        <!-- خطة العلاج -->
                        <div class="col-12">
                            <label class="form-label">
                                خطة العلاج <span class="required-star">*</span>
                            </label>
                            <textarea name="treatment_plan" class="form-control" rows="3" required
                                placeholder="أدخل خطة العلاج..."><?= htmlspecialchars($test_data['treatment_plan'] ?? '') ?></textarea>
                        </div>

                        <!-- ملاحظات الطبيب -->
                        <div class="col-12">
                            <label class="form-label">ملاحظات الطبيب</label>
                            <textarea name="notes" class="form-control" rows="2"
                                placeholder="أدخل أي ملاحظات إضافية..."><?= htmlspecialchars($test_data['doctor_notes'] ?? '') ?></textarea>
                        </div>

                        <!-- أزرار التحكم -->
                        <div class="col-12 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التشخيص
                            </button>
                            <a href="doctor_dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> العودة
                            </a>
                            <a href="patient_report.php?test_id=<?= $test_id ?>" class="btn btn-success">
                                <i class="fas fa-print"></i> طباعة التقرير
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>