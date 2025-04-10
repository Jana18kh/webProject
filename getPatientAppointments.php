<?php
session_start();
header('Content-Type: application/json');

// Check if user is logged in as patient
if (!isset($_SESSION['user_id']) || ($_SESSION['user_type'] ?? '') !== 'patient') {
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

$patientId = $_SESSION['user_id'];

try {
    // Get appointments with doctor details
    $sql = "SELECT 
                a.id, 
                a.date, 
                a.time, 
                a.status, 
                a.reason,
                CONCAT('Dr. ', d.firstName, ' ', d.lastName) AS doctorName,
                d.uniqueFileName AS doctorPhoto,
                s.speciality AS doctorSpecialty
            FROM 
                Appointment a
            JOIN 
                Doctor d ON a.doctor_id = d.id
            JOIN
                Speciality s ON d.SpecialityID = s.id
            WHERE 
                a.patient_id = ?
            ORDER BY 
                a.date, a.time";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patientId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $appointments = [];
    while ($row = $result->fetch_assoc()) {
        // Format date and time
        $dateObj = new DateTime($row['date']);
        $timeObj = new DateTime($row['time']);
        
        $appointments[] = [
            'id' => $row['id'],
            'date' => $dateObj->format('d/m/Y'),
            'time' => $timeObj->format('H:i'),
            'status' => $row['status'],
            'reason' => $row['reason'],
            'doctorName' => $row['doctorName'],
            'doctorPhoto' => 'uploads/' . $row['doctorPhoto'],
            'doctorSpecialty' => $row['doctorSpecialty']
        ];
    }
    
    echo json_encode($appointments);

} catch (Exception $e) {
    error_log("Database error: " . $e->getMessage());
    echo json_encode(['error' => 'Failed to retrieve appointments']);
} finally {
    $conn->close();
}
?>