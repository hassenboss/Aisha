<?php
session_start();
if ($_SESSION['role'] != 'receptionist') {
    header("Location: login.php");
    exit();
}

include('config.php');
$message = '';

// معالجة عمليات الإضافة والتحديث
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['save'])) {
        $patient_id = $_POST['patient_id'] ?? null;
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $date_of_birth = mysqli_real_escape_string($conn, $_POST['date_of_birth']);
        $gender = mysqli_real_escape_string($conn, $_POST['gender']);
        $contact_info = mysqli_real_escape_string($conn, $_POST['contact_info']);
        $medical_history = mysqli_real_escape_string($conn, $_POST['medical_history']);

        if (empty($patient_id)) {
            // إضافة مريض جديد
            $unique_id = str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);
            $query = "INSERT INTO patients (name, date_of_birth, gender, contact_info, medical_history, unique_id)
                     VALUES ('$name', '$date_of_birth', '$gender', '$contact_info', '$medical_history', '$unique_id')";
            $success_message = "تمت إضافة المريض بنجاح!";
        } else {
            // تحديث بيانات المريض
            $query = "UPDATE patients SET 
                     name = '$name',
                     date_of_birth = '$date_of_birth',
                     gender = '$gender',
                     contact_info = '$contact_info',
                     medical_history = '$medical_history'
                     WHERE patient_id = $patient_id";
            $success_message = "تم تحديث البيانات بنجاح!";
        }

        if (mysqli_query($conn, $query)) {
            $message = "<div class='alert alert-success'>$success_message</div>";
        } else {
            $message = "<div class='alert alert-danger'>خطأ في قاعدة البيانات: " . mysqli_error($conn) . "</div>";
        }
    }
}

// معالجة طلب الحذف
if (isset($_GET['delete'])) {
    $patient_id = intval($_GET['delete']);
    $query = "DELETE FROM patients WHERE patient_id = $patient_id";
    
    if (mysqli_query($conn, $query)) {
        $message = "<div class='alert alert-success'>تم حذف المريض بنجاح</div>";
    } else {
        $message = "<div class='alert alert-danger'>خطأ في الحذف: " . mysqli_error($conn) . "</div>";
    }
}

// جلب بيانات المريض للتعديل
$edit_patient = null;
if (isset($_GET['edit'])) {
    $patient_id = intval($_GET['edit']);
    $result = mysqli_query($conn, "SELECT * FROM patients WHERE patient_id = $patient_id");
    $edit_patient = mysqli_fetch_assoc($result);
}

// جلب قائمة المرضى
$patients = mysqli_query($conn, "SELECT * FROM patients ORDER BY patient_id DESC");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المرضى</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
            border-radius: 10px 10px 0 0;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .table {
            margin-top: 20px;
        }
        .table thead {
            background-color: #007bff;
            color: white;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">إدارة المرضى</h1>
        
        <?php echo $message; ?>

        <!-- نموذج الإضافة/التعديل -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><?= $edit_patient ? 'تعديل بيانات المريض' : 'إضافة مريض جديد' ?></h5>
            </div>
            <div class="card-body">
                <form method="post">
                    <input type="hidden" name="patient_id" value="<?= $edit_patient['patient_id'] ?? '' ?>">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" name="name" class="form-control" required 
                                value="<?= htmlspecialchars($edit_patient['name'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">تاريخ الميلاد</label>
                            <input type="date" name="date_of_birth" class="form-control" required
                                value="<?= htmlspecialchars($edit_patient['date_of_birth'] ?? '') ?>">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">الجنس</label>
                            <select name="gender" class="form-select" required>
                                <option value="male" <?= ($edit_patient['gender'] ?? '') == 'male' ? 'selected' : '' ?>>ذكر</option>
                                <option value="female" <?= ($edit_patient['gender'] ?? '') == 'female' ? 'selected' : '' ?>>أنثى</option>
                                <option value="other" <?= ($edit_patient['gender'] ?? '') == 'other' ? 'selected' : '' ?>>أخرى</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">معلومات الاتصال</label>
                            <input type="text" name="contact_info" class="form-control"
                                value="<?= htmlspecialchars($edit_patient['contact_info'] ?? '') ?>">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">السجل الطبي</label>
                            <textarea name="medical_history" class="form-control" rows="3"><?= htmlspecialchars($edit_patient['medical_history'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" name="save" class="btn btn-primary">
                            <?= $edit_patient ? 'تحديث' : 'إضافة' ?>
                        </button>
                        <?php if($edit_patient): ?>
                            <a href="manage_patients.php" class="btn btn-secondary">إلغاء التعديل</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- قائمة المرضى -->
        <div class="card shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">قائمة المرضى</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>تاريخ الميلاد</th>
                                <th>الجنس</th>
                                <th>الاتصال</th>
                                <th>السجل الطبي</th>
                                <th>الرقم الفريد</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($patient = mysqli_fetch_assoc($patients)): ?>
                            <tr>
                                <td><?= htmlspecialchars($patient['name']) ?></td>
                                <td><?= htmlspecialchars($patient['date_of_birth']) ?></td>
                                <td><?= htmlspecialchars($patient['gender']) ?></td>
                                <td><?= htmlspecialchars($patient['contact_info']) ?></td>
                                <td><?= nl2br(htmlspecialchars($patient['medical_history'])) ?></td>
                                <td><?= htmlspecialchars($patient['unique_id']) ?></td>
                                <td>
                                    <a href="manage_patients.php?edit=<?= $patient['patient_id'] ?>" class="btn btn-sm btn-warning">تعديل</a>
                                    <a href="manage_patients.php?delete=<?= $patient['patient_id'] ?>" 
                                       class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>