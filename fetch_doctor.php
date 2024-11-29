<?php
include 'db_connection.php';

// Fetch doctor details
$sql = "SELECT id, name, qualification, specialty FROM doctors WHERE id = 1"; // Assuming you want the doctor with id 1
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    $doctor = $result->fetch_assoc();
    echo json_encode($doctor);
} else {
    echo json_encode([]);
}

$conn->close();
?>