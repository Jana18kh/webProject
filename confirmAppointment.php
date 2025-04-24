<?php
// Start the session
session_start();

// Database connection
$servername = "sql110.infinityfree.com";
$username = "if0_38818376";
$password = "zWf9MgaDKqcc";
$dbname = "if0_38818376_curego";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the id parameter is set in the URL
if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];

    // Update the status to "Confirmed" for the corresponding appointment
    $sql = "UPDATE Appointment SET status = 'Confirmed' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();

    // Redirect back to the doctor homepage
    header("Location: doctorHomepage.php");
    exit();
} else {
    echo "Appointment ID is missing!";
}

// Close the connection
$conn->close();
?>
