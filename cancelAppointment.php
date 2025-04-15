<?php
// Start the session
session_start();

// Check if user is logged in as a patient
if (!isset($_SESSION['user_id'])) {
    header("Location: homepage.html");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "curego";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the id parameter is set in the URL
if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];
    $patient_id = $_SESSION['user_id'];

    // First verify the appointment belongs to the logged-in patient
    $verify_sql = "SELECT id FROM Appointment WHERE id = ? AND PatientID = ?";
    $verify_stmt = $conn->prepare($verify_sql);
    $verify_stmt->bind_param("ii", $appointment_id, $patient_id);
    $verify_stmt->execute();
    $verify_result = $verify_stmt->get_result();
    $appointment = $verify_result->fetch_assoc();

    if (!$appointment || !in_array($appointment['status'], ['Pending', 'Confirmed'])) {
        header("Location: patientHomepage.php?error=invalid_status");
        exit();
    }

    // Delete the appointment
    $delete_sql = "DELETE FROM Appointment WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $appointment_id);

    if ($delete_stmt->execute()) {
        // Success - redirect back with success message
        header("Location: patientHomepage.php?success=appointment_cancelled");
    } else {
        // Error - redirect back with error message
        error_log("Appointment deletion failed: " . $conn->error);
        header("Location: patientHomepage.php?error=cancel_failed");
    }
} else {
    header("Location: patientHomepage.php?error=invalid_appointment");
}

// Close the connection
$conn->close();
exit();
?>
