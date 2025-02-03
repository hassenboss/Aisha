<?php
session_start();
if ($_SESSION['role'] != 'receptionist') {
    header("Location: login.php");
    exit();
}
include('config.php');

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['schedule'])) {
        $doctor_id = $_POST['doctor_id'];
        $patient_id = $_POST['patient_id'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $status = 'scheduled';

        $query = "INSERT INTO appointments (doctor_id, patient_id, date, time, status) VALUES ('$doctor_id', '$patient_id', '$date', '$time', '$status')";

        if (mysqli_query($conn, $query)) {
            $message = "Appointment scheduled successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['edit'])) {
        $appointment_id = $_POST['appointment_id'];
        $doctor_id = $_POST['doctor_id'];
        $patient_id = $_POST['patient_id'];
        $date = $_POST['date'];
        $time = $_POST['time'];
        $status = $_POST['status'];

        $query = "UPDATE appointments SET doctor_id='$doctor_id', patient_id='$patient_id', date='$date', time='$time', status='$status' WHERE appointment_id=$appointment_id";

        if (mysqli_query($conn, $query)) {
            $message = "Appointment updated successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

if (isset($_GET['delete'])) {
    $appointment_id = $_GET['delete'];
    $query = "DELETE FROM appointments WHERE appointment_id=$appointment_id";
    if (mysqli_query($conn, $query)) {
        $message = "Appointment deleted successfully!";
    } else {
        $message = "Error: " . mysqli_error($conn);
    }
}

$doctors_query = "SELECT * FROM users WHERE role='doctor'";
$doctors_result = mysqli_query($conn, $doctors_query);

$appointments_query = "SELECT a.*, p.name as patient_name, d.name as doctor_name FROM appointments a JOIN patients p ON a.patient_id = p.patient_id JOIN users d ON a.doctor_id = d.id";
$appointments_result = mysqli_query($conn, $appointments_query);

if (isset($_GET['edit'])) {
    $edit_appointment_id = $_GET['edit'];
    $edit_query = "SELECT * FROM appointments WHERE appointment_id=$edit_appointment_id";
    $edit_result = mysqli_query($conn, $edit_query);
    $edit_appointment = mysqli_fetch_assoc($edit_result);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Appointments</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .table-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Manage Appointments</h1>

        <!-- Form Section -->
        <div class="form-container">
            <form method="post" action="manage_appointments.php">
                <input type="hidden" name="appointment_id" value="<?php echo isset($_GET['edit']) ? $_GET['edit'] : ''; ?>">
                <div class="mb-3">
                    <label for="doctor_id" class="form-label">Doctor:</label>
                    <select class="form-select" name="doctor_id" required>
                        <?php mysqli_data_seek($doctors_result, 0); ?>
                        <?php while ($row = mysqli_fetch_assoc($doctors_result)) { ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo (isset($_GET['edit']) && $edit_appointment['doctor_id'] == $row['id']) ? 'selected' : ''; ?>><?php echo $row['name']; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="patient_id" class="form-label">Patient ID:</label>
                    <input type="number" class="form-control" name="patient_id" value="<?php echo isset($_GET['edit']) ? $edit_appointment['patient_id'] : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="date" class="form-label">Date:</label>
                    <input type="date" class="form-control" name="date" value="<?php echo isset($_GET['edit']) ? $edit_appointment['date'] : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="time" class="form-label">Time:</label>
                    <input type="time" class="form-control" name="time" value="<?php echo isset($_GET['edit']) ? $edit_appointment['time'] : ''; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="status" class="form-label">Status:</label>
                    <select class="form-select" name="status" required>
                        <option value="scheduled" <?php echo (isset($_GET['edit']) && $edit_appointment['status'] == 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                        <option value="completed" <?php echo (isset($_GET['edit']) && $edit_appointment['status'] == 'completed') ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo (isset($_GET['edit']) && $edit_appointment['status'] == 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" name="<?php echo isset($_GET['edit']) ? 'edit' : 'schedule'; ?>">
                    <?php echo isset($_GET['edit']) ? 'Update Appointment' : 'Schedule Appointment'; ?>
                </button>
            </form>
            <?php if (!empty($message)) { ?>
                <div class="alert alert-<?php echo strpos($message, 'Error') === false ? 'success' : 'danger'; ?> mt-3">
                    <?php echo $message; ?>
                </div>
            <?php } ?>
        </div>

        <!-- Table Section -->
        <div class="table-container">
            <h2>Appointments List</h2>
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Doctor</th>
                        <th>Patient</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php mysqli_data_seek($appointments_result, 0); ?>
                    <?php while ($row = mysqli_fetch_assoc($appointments_result)) { ?>
                        <tr>
                            <td><?php echo $row['doctor_name']; ?></td>
                            <td><?php echo $row['patient_name']; ?></td>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['time']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <a href="manage_appointments.php?edit=<?php echo $row['appointment_id']; ?>" class="btn btn-warning btn-sm btn-custom">Edit</a>
                                <a href="manage_appointments.php?delete=<?php echo $row['appointment_id']; ?>" class="btn btn-danger btn-sm btn-custom" onclick="return confirm('Are you sure you want to delete this appointment?');">Delete</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bootstrap JS (Optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>