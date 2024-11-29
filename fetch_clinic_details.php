<?php
include 'db_connection.php';

$clinicID = $_GET['clinicID'] ?? 0;

$sql = "SELECT address FROM clinics WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $clinicID);
$stmt->execute();
$result = $stmt->get_result();
$clinic = $result->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($clinic);
?>
