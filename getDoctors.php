<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in (either as patient or doctor)
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "curego";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

// Get and sanitize specialty ID
$specialtyId = isset($_GET['specialtyId']) ? (int)$_GET['specialtyId'] : null;

if (!$specialtyId) {
    echo json_encode(['error' => 'Specialty ID is required']);
    $conn->close();
    exit();
}

try {
    // Get doctors by specialty with their speciality name
    $sql = "SELECT d.id, d.firstName, d.lastName, d.uniqueFileName, s.speciality 
            FROM Doctor d
            JOIN Speciality s ON d.SpecialityID = s.id
            WHERE d.SpecialityID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $specialtyId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = [
            'id' => $row['id'],
            'firstName' => $row['firstName'],
            'lastName' => $row['lastName'],
            'speciality' => $row['speciality'],
            'photo' => 'uploads/' . $row['uniqueFileName']
        ];
    }
    
    echo json_encode($doctors);

} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Failed to retrieve doctors']);
} finally {
    $conn->close();
}
?>