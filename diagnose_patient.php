<?php
session_start();
if ($_SESSION['role'] != 'doctor') {
    header("Location: login.php");
    exit();
}
include('config.php');

if (isset($_GET['id'])) {
    $patient_id = $_GET['id'];
    $patient_query = "SELECT * FROM patients WHERE patient_id='$patient_id'";
    $patient_result = mysqli_query($conn, $patient_query);
    $patient = mysqli_fetch_assoc($patient_result);

    $lab_tests_query = "SELECT test_id, test_name FROM lab_tests_catalog";
    $lab_tests_result = mysqli_query($conn, $lab_tests_query);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $diagnosis = $_POST['diagnosis'];
        $lab_tests = implode(', ', $_POST['lab_tests']);
        
        $update_query = "UPDATE patients SET medical_history=CONCAT(medical_history, '\nDiagnosis: ', '$diagnosis', '\nLab Tests: ', '$lab_tests') WHERE patient_id='$patient_id'";
        
        if (mysqli_query($conn, $update_query)) {
            $message = "Diagnosis updated successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
} else {
    header("Location: view_patients.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Diagnose Patient</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles_diagnose_patient.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Diagnose Patient</h1>
        <div class="card mt-4">
            <div class="card-body">
                <form method="post" action="diagnose_patient.php?id=<?php echo $patient_id; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Patient Name:</label>
                        <input type="text" class="form-control" id="name" value="<?php echo $patient['name']; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="diagnosis" class="form-label">Diagnosis:</label>
                        <textarea class="form-control" id="diagnosis" name="diagnosis" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="lab_tests" class="form-label">Lab Tests Required:</label>
                        <select class="form-select" id="lab_tests" name="lab_tests[]" multiple>
                            <?php while ($row = mysqli_fetch_assoc($lab_tests_result)) { ?>
                                <option value="<?php echo $row['test_name']; ?>"><?php echo $row['test_name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Submit Diagnosis</button>
                </form>
                <?php if (isset($message)) { echo "<div class='alert alert-success mt-3'>$message</div>"; } ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
