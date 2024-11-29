<?php
session_start(); // Start the session
include 'db_connection.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if the user is logged in and has the required session variables
if (!isset($_SESSION['user_id'], $_SESSION['user_email'])) {
    echo json_encode(['message' => 'User  not logged in']);
    exit;
}

// Get user details from session
$userID = $_SESSION['user_id'];
$email = $_SESSION['user_email'];

// Get the slot ID from the request
$data = json_decode(file_get_contents('php://input'), true);
error_log("Received data: " . print_r($data, true)); // Debugging output

$slotID = $data['id'] ?? null; // Assuming slot ID is passed in the input
$clinicID = $data['clinic_id'] ?? null;
$doctorID = $data['doctor_id'] ?? null;
$appointmentDate = $data['appointment_date'] ?? null;

// Check if slot ID is provided
if ($slotID === null) {
    echo json_encode(['message' => 'Slot ID is required']);
    exit;
}

// Prepare and execute the query to check if the slot is booked
$sql = "SELECT is_booked FROM slots WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $slotID);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Check if the slot is already booked
if ($row === null || $row['is_booked']) {
    echo json_encode(['message' => 'Slot already booked']);
    exit;
}

// Begin transaction
$conn->begin_transaction();

try {
    // Update the slot to mark it as booked
    $updateSlot = "UPDATE slots SET is_booked = 1 WHERE id = ?";
    $stmt = $conn->prepare($updateSlot);
    $stmt->bind_param('i', $slotID);
    if (!$stmt->execute()) {
        throw new Exception("Failed to update slot: " . $stmt->error);
    }

    // Insert the appointment
    $insertAppointment = "INSERT INTO appointments (slot_id, user_email, booking_date, clinic_id, doctor_id) VALUES (?, ?, NOW(), ?, ?)";
    $stmt = $conn ->prepare($insertAppointment);
    $stmt->bind_param('isii', $slotID, $email, $clinicID, $doctorID); // Correctly bind parameters
    if (!$stmt->execute()) {
        throw new Exception("Failed to insert appointment: " . $stmt->error);
    }

    // Commit the transaction
    $conn->commit();
    echo json_encode(['message' => 'Slot booked successfully']);
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    error_log("Transaction failed: " . $e->getMessage());
    echo json_encode(['message' => 'Error booking slot: ' . $e->getMessage()]); // Include the error message
}
?>