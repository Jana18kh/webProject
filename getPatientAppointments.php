<?php
header('Content-Type: application/json');

require 'db_connection.php';

// In a real app, you would get patient ID from session
$patientId = 1;

$stmt = $pdo->prepare("
    SELECT a.id, a.date, a.time, a.status, a.reason,
           CONCAT('Dr. ', d.firstName, ' ', d.lastName) AS doctorName,
           d.uniqueFileName AS doctorPhoto
    FROM appointment a
    JOIN doctor d ON a.doctor_id = d.id
    WHERE a.patient_id = ?
    ORDER BY a.date, a.time
");
$stmt->execute([$patientId]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($appointments);
?>