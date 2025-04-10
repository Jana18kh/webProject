<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in as patient
if (!isset($_SESSION['user_id']) {
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

// Get patient ID from session
$patientId = $_SESSION['user_id'];
$doctorId = $conn->real_escape_string($_POST['doctor'] ?? null);
$date = $conn->real_escape_string($_POST['date'] ?? null);
$time = $conn->real_escape_string($_POST['time'] ?? null);
$reason = $conn->real_escape_string($_POST['reason'] ?? null);

// Validate inputs
if (!$doctorId || !$date || !$time || !$reason) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    $conn->close();
    exit();
}

// Validate date format
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    echo json_encode(['success' => false, 'message' => 'Invalid date format']);
    $conn->close();
    exit();
}

// Validate time format
if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
    echo json_encode(['success' => false, 'message' => 'Invalid time format']);
    $conn->close();
    exit();
}

try {
    // Check if doctor exists
    $checkDoctor = $conn->prepare("SELECT id FROM Doctor WHERE id = ?");
    $checkDoctor->bind_param("i", $doctorId);
    $checkDoctor->execute();
    
    if ($checkDoctor->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Doctor not found']);
        $conn->close();
        exit();
    }

    // Check for duplicate appointments
    $checkDuplicate = $conn->prepare("
        SELECT id FROM Appointment 
        WHERE patient_id = ? 
        AND doctor_id = ? 
        AND date = ? 
        AND time = ?
    ");
    $checkDuplicate->bind_param("iiss", $patientId, $doctorId, $date, $time);
    $checkDuplicate->execute();
    
    if ($checkDuplicate->get_result()->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'You already have this appointment scheduled']);
        $conn->close();
        exit();
    }

    // Insert new appointment
    $stmt = $conn->prepare("
        INSERT INTO Appointment (
            patient_id, 
            doctor_id, 
            date, 
            time, 
            reason, 
            status,
            created_at
        ) VALUES (?, ?, ?, ?, ?, 'Pending', NOW())
    ");
    $stmt->bind_param("iisss", $patientId, $doctorId, $date, $time, $reason);
    
    if ($stmt->execute()) {
        // Get the new appointment details for response
        $newAppointmentId = $stmt->insert_id;
        $getAppointment = $conn->query("
            SELECT a.*, d.firstName, d.lastName 
            FROM Appointment a
            JOIN Doctor d ON a.doctor_id = d.id
            WHERE a.id = $newAppointmentId
        ");
        
        echo json_encode([
            'success' => true,
            'appointment' => $getAppointment->fetch_assoc()
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to book appointment']);
    }

} catch (Exception $e) {
    error_log("Appointment error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
} finally {
    $conn->close();
}
?>