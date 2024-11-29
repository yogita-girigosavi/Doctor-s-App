<?php
include 'db_connection.php';

$date = $_GET['date'] ?? date('Y-m-d');

$sql = "SELECT id, time_slot, is_booked FROM slots WHERE appointment_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $date);
$stmt->execute();
$result = $stmt->get_result();

$slots = [];
while ($row = $result->fetch_assoc()) {
    $slots[] = $row;
}

header('Content-Type: application/json');
echo json_encode($slots);
?>
