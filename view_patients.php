<?php
session_start();
if ($_SESSION['role'] != 'doctor') {
    header("Location: login.php");
    exit();
}
include('config.php');

$search = '';
if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $patients_query = "SELECT * FROM patients WHERE name LIKE '%$search%' OR unique_id LIKE '%$search%'";
} else {
    $patients_query = "SELECT * FROM patients";
}
$patients_result = mysqli_query($conn, $patients_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles_view_patients.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">View Patients</h1>
        <div class="mt-4">
            <form class="d-flex mb-4" method="get" action="view_patients.php">
                <input class="form-control me-2" type="search" name="search" placeholder="Search by Name or ID" value="<?php echo $search; ?>">
                <button class="btn btn-primary" type="submit">Search</button>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Name</th>
                            <th>Date of Birth</th>
                            <th>Gender</th>
                            <th>Contact Info</th>
                            <th>Medical History</th>
                            <th>Unique ID</th>
                            <th>Action</th>
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
                                <td><a href="diagnose_patient.php?id=<?php echo $row['patient_id']; ?>" class="btn btn-success">Diagnose</a></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
