<?php
session_start();
if ($_SESSION['role'] != 'lab_technician') {
    header("Location: login.php");
    exit();
}
include('config.php');

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $appointments_query = "SELECT a.*, p.name as patient_name, d.name as doctor_name, p.medical_history FROM appointments a JOIN patients p ON a.patient_id = p.patient_id JOIN users d ON a.doctor_id = d.id WHERE a.status='scheduled' AND (p.name LIKE '%$search%' OR p.unique_id LIKE '%$search%')";
} else {
    $appointments_query = "SELECT a.*, p.name as patient_name, d.name as doctor_name, p.medical_history FROM appointments a JOIN patients p ON a.patient_id = p.patient_id JOIN users d ON a.doctor_id = d.id WHERE a.status='scheduled'";
}
$appointments_result = mysqli_query($conn, $appointments_query);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit_results'])) {
        $appointment_id = $_POST['appointment_id'];
        $test_results = $_POST['test_results'];
        
        $query = "INSERT INTO lab_tests (appointment_id, test_results) VALUES ('$appointment_id', '$test_results')";
        
        if (mysqli_query($conn, $query)) {
            $message = "Test results submitted successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Tests</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles_manage_tests.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Manage Tests</h1>
        <div class="card mt-4">
            <div class="card-body">
                <form method="post" action="manage_tests.php">
                    <div class="mb-3">
                        <label for="appointment_id" class="form-label">Appointment ID:</label>
                        <select class="form-select" id="appointment_id" name="appointment_id" required>
                            <?php
                            // إعادة تعيين مؤشر النتائج قبل عرضها في القائمة المنسدلة
                            mysqli_data_seek($appointments_result, 0);
                            while ($row = mysqli_fetch_assoc($appointments_result)) { ?>
                                <option value="<?php echo $row['appointment_id']; ?>">Patient: <?php echo $row['patient_name']; ?> | Doctor: <?php echo $row['doctor_name']; ?> | Date: <?php echo $row['date']; ?> | Time: <?php echo $row['time']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="test_results" class="form-label">Test Results:</label>
                        <textarea class="form-control" id="test_results" name="test_results" required></textarea>
                    </div>
                    <button type="submit" name="submit_results" class="btn btn-primary w-100">Submit Results</button>
                </form>
                <?php if (isset($message)) { echo "<div class='alert alert-success mt-3'>$message</div>"; } ?>
            </div>
        </div>
        <div class="mt-4">
            <form class="d-flex mb-4" method="get" action="manage_tests.php">
                <input class="form-control me-2" type="search" name="search" placeholder="Search by Name or ID" value="<?php echo $search; ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Patient</th>
                        <th>Doctor</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Medical History</th>
                        <th>Tests Required</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    mysqli_data_seek($appointments_result, 0); // لإعادة تعيين مؤشر النتائج
                    while ($row = mysqli_fetch_assoc($appointments_result)) {
                        $medical_history = explode('\n', $row['medical_history']);
                        $tests_required = '';
                        foreach ($medical_history as $line) {
                            if (strpos($line, 'Lab Tests: ') !== false) {
                                $tests_required = str_replace('Lab Tests: ', '', $line);
                                break;
                            }
                        }
                    ?>
                        <tr>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td><?php echo $row['doctor_name']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['time']; ?></td>
                            <td><?php echo nl2br($row['medical_history']); ?></td>
                            <td><?php echo $tests_required; ?></td>
                            <td><?php echo $row['status']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
