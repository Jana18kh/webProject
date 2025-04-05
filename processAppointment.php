<?php
header('Content-Type: application/json');

require 'db_connection.php';

// In a real app, you would get patient ID from session
$patientId = 1; // This should come from session
$doctorId = $_POST['doctor'] ?? null;
$date = $_POST['date'] ?? null;
$time = $_POST['time'] ?? null;
$reason = $_POST['reason'] ?? null;

if (!$doctorId || !$date || !$time || !$reason) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO appointment (patient_id, doctor_id, date, time, reason, status) 
                          VALUES (?, ?, ?, ?, ?, 'Pending')");
    $stmt->execute([$patientId, $doctorId, $date, $time, $reason]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>