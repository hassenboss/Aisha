<?php
session_start();
if ($_SESSION['role'] != 'receptionist') {
    header("Location: login.php");
    exit();
}
include('config.php');

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $date_of_birth = $_POST['date_of_birth'];
        $gender = $_POST['gender'];
        $contact_info = $_POST['contact_info'];
        $medical_history = $_POST['medical_history'];
        $unique_id = str_pad(mt_rand(1, 9999999), 7, '0', STR_PAD_LEFT);

        $query = "INSERT INTO patients (name, date_of_birth, gender, contact_info, medical_history, unique_id) VALUES ('$name', '$date_of_birth', '$gender', '$contact_info', '$medical_history', '$unique_id')";

        if (mysqli_query($conn, $query)) {
            $message = "Patient added successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['update'])) {
        $patient_id = $_POST['patient_id'];
        $name = $_POST['name'];
        $date_of_birth = $_POST['date_of_birth'];
        $gender = $_POST['gender'];
        $contact_info = $_POST['contact_info'];
        $medical_history = $_POST['medical_history'];

        $query = "UPDATE patients SET name='$name', date_of_birth='$date_of_birth', gender='$gender', contact_info='$contact_info', medical_history='$medical_history' WHERE patient_id='$patient_id'";

        if (mysqli_query($conn, $query)) {
            $message = "Patient updated successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    } elseif (isset($_POST['delete'])) {
        $patient_id = $_POST['patient_id'];

        $query = "DELETE FROM patients WHERE patient_id='$patient_id'";

        if (mysqli_query($conn, $query)) {
            $message = "Patient deleted successfully!";
        } else {
            $message = "Error: " . mysqli_error($conn);
        }
    }
}

$patients_query = "SELECT * FROM patients";
$patients_result = mysqli_query($conn, $patients_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Patients</title>
    <link rel="stylesheet" href="styles2.css">
</head>
<body>
    <h1>Manage Patients</h1>
    <div class="form-container">
        <form method="post" action="manage_patients.php">
            <input type="hidden" name="patient_id">
            <label>Name:</label>
            <input type="text" name="name" required>
            <label>Date of Birth:</label>
            <input type="date" name="date_of_birth" required>
            <label>Gender:</label>
            <select name="gender" required>
                <option value="male">Male</option>
                <option value="female">Female</option>
                <option value="other">Other</option>
            </select>
            <label>Contact Info:</label>
            <input type="text" name="contact_info">
            <label>Medical History:</label>
            <textarea name="medical_history"></textarea>
            <button type="submit" name="add">Add Patient</button>
            <button type="submit" name="update">Update Patient</button>
            <button type="submit" name="delete">Delete Patient</button>
        </form>
        <?php if (isset($message)) { echo "<p>$message</p>"; } ?>
    </div>
    <div class="table-container">
        <h2>Patients List</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Date of Birth</th>
                    <th>Gender</th>
                    <th>Contact Info</th>
                    <th>Medical History</th>
                    <th>Unique ID</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($patients_result)) { ?>
                    <tr>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['date_of_birth']; ?></td>
                        <td><?php echo $row['gender']; ?></td>
                        <td><?php echo $row['contact_info']; ?></td>
                        <td><?php echo $row['medical_history']; ?></td>
                        <td><?php echo $row['unique_id']; ?></td>
                        <td>
                            <form method="post" action="manage_patients.php">
                                <input type="hidden" name="patient_id" value="<?php echo $row['patient_id']; ?>">
                                <input type="hidden" name="name" value="<?php echo $row['name']; ?>">
                                <input type="hidden" name="date_of_birth" value="<?php echo $row['date_of_birth']; ?>">
                                <input type="hidden" name="gender" value="<?php echo $row['gender']; ?>">
                                <input type="hidden" name="contact_info" value="<?php echo $row['contact_info']; ?>">
                                <input type="hidden" name="medical_history" value="<?php echo $row['medical_history']; ?>">
                                <button type="submit" name="edit">Edit</button>
                                <button type="submit" name="delete">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <script>
        document.querySelectorAll('button[name="edit"]').forEach(button => {
            button.addEventListener('click', function () {
                const row = this.closest('tr');
                const form = document.querySelector('.form-container form');
                form.querySelector('input[name="patient_id"]').value = row.querySelector('input[name="patient_id"]').value;
                form.querySelector('input[name="name"]').value = row.querySelector('input[name="name"]').value;
                form.querySelector('input[name="date_of_birth"]').value = row.querySelector('input[name="date_of_birth"]').value;
                form.querySelector('select[name="gender"]').value = row.querySelector('input[name="gender"]').value;
                form.querySelector('input[name="contact_info"]').value = row.querySelector('input[name="contact_info"]').value;
                form.querySelector('textarea[name="medical_history"]').value = row.querySelector('input[name="medical_history"]').value;
            });
        });
    </script>
</body>
</html>
