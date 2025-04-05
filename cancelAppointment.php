<?php
header('Content-Type: application/json');

require 'db_connection.php';

$appointmentId = $_GET['id'] ?? null;

if (!$appointmentId) {
    echo json_encode(['success' => false, 'message' => 'Appointment ID is required']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM appointment WHERE id = ?");
    $stmt->execute([$appointmentId]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Appointment not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>