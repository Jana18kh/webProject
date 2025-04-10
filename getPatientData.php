<?php
header('Content-Type: application/json');
require 'db_connection.php'; // Assuming this file establishes your database connection

// Start session and check authentication
session_start();
if (!isset($_SESSION['patient_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

$patientId = $_SESSION['patient_id'];

try {
    // Prepare and execute query
    $stmt = $pdo->prepare("
        SELECT 
            id,
            first_name AS firstName,
            last_name AS lastName,
            email,
            date_of_birth AS dob,
            gender,
            phone_number AS phone
        FROM patients 
        WHERE id = ?
    ");
    $stmt->execute([$patientId]);
    $patientData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$patientData) {
        echo json_encode(['error' => 'Patient not found']);
        exit;
    }

    // Format date if needed
    if ($patientData['dob']) {
        $patientData['dob'] = date('d/m/Y', strtotime($patientData['dob']));
    }

    // Return JSON response
    echo json_encode([
        'success' => true,
        'data' => $patientData
    ]);

} catch (PDOException $e) {
    // Log error for debugging
    error_log("Database error: " . $e->getMessage());
    
    echo json_encode([
        'error' => 'Database error',
        'message' => 'Could not retrieve patient data'
    ]);
}
?>