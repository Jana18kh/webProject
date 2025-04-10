<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "curego";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit();
}

$appointmentId = $conn->real_escape_string($_GET['id'] ?? null);

if (!$appointmentId) {
    echo json_encode(['success' => false, 'message' => 'Appointment ID is required']);
    $conn->close();
    exit();
}

try {
    // Verify the appointment belongs to the current user (either patient or doctor)
    $userId = $_SESSION['user_id'];
    $userType = $_SESSION['user_type'] ?? null;
    
    $verifySql = "";
    if ($userType === 'patient') {
        $verifySql = "SELECT id FROM Appointment WHERE id = ? AND patient_id = ?";
    } elseif ($userType === 'doctor') {
        $verifySql = "SELECT id FROM Appointment WHERE id = ? AND doctor_id = ?";
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid user type']);
        $conn->close();
        exit();
    }
    
    $verifyStmt = $conn->prepare($verifySql);
    $verifyStmt->bind_param("ii", $appointmentId, $userId);
    $verifyStmt->execute();
    $verifyResult = $verifyStmt->get_result();
    
    if ($verifyResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Appointment not found or access denied']);
        $conn->close();
        exit();
    }

    // Update status to Cancelled instead of deleting (better for records)
    $updateStmt = $conn->prepare("UPDATE Appointment SET status = 'Cancelled' WHERE id = ?");
    $updateStmt->bind_param("i", $appointmentId);
    
    if ($updateStmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to cancel appointment']);
    }

} catch (Exception $e) {
    error_log("Cancel appointment error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
} finally {
    $conn->close();
}
?>