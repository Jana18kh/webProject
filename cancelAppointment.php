<?php
// Start the session
session_start();

// Check if user is logged in as a patient
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit();
}

// Database connection
$servername = "sql110.infinityfree.com";
$username = "if0_38818376";
$password = "VZrJJdHAmkIV";
$dbname = "if0_38818376_curego";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'db_connection_failed']);
    exit();
}

// Check if the id parameter is set in the POST data
if (isset($_POST['id'])) {
    $appointment_id = $_POST['id'];
    $patient_id = $_SESSION['user_id'];

    // First verify the appointment belongs to the logged-in patient
    $verify_sql = "SELECT id, status FROM Appointment WHERE id = ? AND PatientID = ?";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param("ii", $appointment_id, $patient_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    $appointment = $verify_result->fetch_assoc();

    if (!$appointment || !in_array($appointment['status'], ['Pending', 'Confirmed'])) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'invalid_status']);
        exit();
    }

    // Delete prescriptions linked to this appointment
    $delete_prescriptions_sql = "DELETE FROM Prescription WHERE AppointmentID = ?";
    $delete_prescriptions_stmt = $conn->prepare($delete_prescriptions_sql);
    $delete_prescriptions_stmt->bind_param("i", $appointment_id);
    $delete_prescriptions_stmt->execute();

    // Delete the appointment
    $delete_sql = "DELETE FROM Appointment WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $appointment_id);

    if ($delete_stmt->execute()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'delete_failed']);
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => 'invalid_appointment']);
}

// Close the connection
$conn->close();
exit();
?>
