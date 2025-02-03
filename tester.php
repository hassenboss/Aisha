<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'lab_technician' && $_SESSION['role'] != 'doctor')) {
    header("Location: login.php");
    exit();
}

include('config.php');

$message = '';

// معالجة إرسال النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $patient_id = intval($_POST['patient_id']);
    $test_name = mysqli_real_escape_string($conn, $_POST['test_name']);
    $test_type = mysqli_real_escape_string($conn, $_POST['test_type']);
    $priority = mysqli_real_escape_string($conn, $_POST['priority']);
    $notes = mysqli_real_escape_string($conn, $_POST['notes']);

    $query = "INSERT INTO lab_tests 
              (patient_id, test_name, test_type, priority, notes, request_date) 
              VALUES 
              ($patient_id, '$test_name', '$test_type', '$priority', '$notes', NOW())";

    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert alert-success'>تم إضافة الفحص بنجاح</div>";
    } else {
        $message = "<div class='alert alert-danger'>خطأ في الإضافة: " . mysqli_error($conn) . "</div>";
    }
}

// جلب قائمة المرضى
$patients = mysqli_query($conn, "SELECT patient_id, name, unique_id FROM patients ORDER BY name");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة فحص معمل</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .lab-header {
            background: linear-gradient(135deg, #17a2b8, #138496);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }
        .form-container {
            background-color: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-label {
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .priority-badge {
            font-size: 0.8em;
            padding: 0.35em 0.65em;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <!-- رأس الصفحة -->
        <div class="lab-header">
            <h1><i class="fas fa-microscope"></i> إضافة فحص معمل جديد</h1>
            <p class="lead">أدخل تفاصيل الفحص المطلوب</p>
        </div>

        <!-- رسائل التنبيه -->
        <?php if(!empty($message)) echo $message; ?>

        <!-- نموذج الإضافة -->
        <div class="form-container">
            <form method="post">
                <div class="row g-3">
                    <!-- اختيار المريض -->
                    <div class="col-md-6">
                        <label class="form-label">اختيار المريض</label>
                        <select name="patient_id" class="form-select" required>
                            <option value="">-- اختر المريض --</option>
                            <?php while($patient = mysqli_fetch_assoc($patients)): ?>
                                <option value="<?= $patient['patient_id'] ?>">
                                    <?= htmlspecialchars($patient['name']) ?> 
                                    (<?= htmlspecialchars($patient['unique_id']) ?>)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <!-- اسم الفحص -->
                    <div class="col-md-6">
                        <label class="form-label">اسم الفحص</label>
                        <input type="text" name="test_name" class="form-control" required>
                    </div>

                    <!-- نوع الفحص -->
                    <div class="col-md-6">
                        <label class="form-label">نوع الفحص</label>
                        <select name="test_type" class="form-select" required>
                            <option value="blood">فحص دم</option>
                            <option value="urine">فحص بول</option>
                            <option value="xray">أشعة</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>

                    <!-- الأولوية -->
                    <div class="col-md-6">
                        <label class="form-label">الأولوية</label>
                        <select name="priority" class="form-select" required>
                            <option value="normal">عادية</option>
                            <option value="high">طارئة</option>
                        </select>
                    </div>

                    <!-- ملاحظات -->
                    <div class="col-12">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- زر الإرسال -->
                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> إضافة الفحص
                        </button>
                        <a href="lab_dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> رجوع
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>