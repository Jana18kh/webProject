<?php
header('Content-Type: application/json');

require 'db_connection.php';

$specialtyId = $_GET['specialtyId'] ?? null;

if ($specialtyId) {
    $stmt = $pdo->prepare("SELECT id, firstName, lastName FROM doctor WHERE SpecialityID = ?");
    $stmt->execute([$specialtyId]);
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($doctors);
} else {
    echo json_encode([]);
}
?>