<?php
// Start the session and database connection
session_start();

$servername = "sql110.infinityfree.com";  // Hostname
$username = "if0_38818376";              // Username
$password = "VZrJJdHAmkIV";               // Password
$dbname = "if0_38818376_curego";         // Database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the id parameter is passed via POST
if (isset($_POST['id'])) {
    $appointment_id = $_POST['id'];

    // Update the status to "Confirmed" in the database
    $sql = "UPDATE Appointment SET status = 'Confirmed' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();

    // Check if the update was successful
    if ($stmt->affected_rows > 0) {
        echo "true";  // Return true if the update was successful
    } else {
        echo "false";  // Return false if something went wrong
    }

    $stmt->close();
} else {
    echo "false";  // Return false if id is not provided
}

$conn->close();
?>
