<?php
include 'db_connection.php';

// Fetch fee details
$doctor_id = 1; // Assuming you want the fees for the doctor with id 1
$sql = "SELECT first_visit_fee, follow_up_fee FROM fees WHERE doctor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Output data of each row
    $fees = $result->fetch_assoc();
    echo json_encode($fees);
} else {
    // Return a message instead of an empty array
    echo json_encode(['first_visit_fee' => 0, 'follow_up_fee' => 0]);
}

$stmt->close();
$conn->close();
?>