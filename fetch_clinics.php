<?php
include 'db_connection.php'; // Include your database connection

$sql = "SELECT id, name FROM clinics"; // Adjust table and column names as per your database
$result = $conn->query($sql);

$clinics = [];
while ($row = $result->fetch_assoc()) {
    $clinics[] = $row;
}

header('Content-Type: application/json');
echo json_encode($clinics);
?>
