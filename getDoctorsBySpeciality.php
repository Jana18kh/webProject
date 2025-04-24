<?php
$servername = "sql110.infinityfree.com";  // Hostname
$username = "if0_38818376";              // Username
$password = "zWf9MgaDKqcc";               // Password
$dbname = "if0_38818376_curego";         // Database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["speciality_id"])) {
    $specialityID = $conn->real_escape_string($_POST["speciality_id"]);
    $query = "SELECT id, CONCAT(firstName, ' ', lastName) AS name FROM Doctor WHERE SpecialityID = '$specialityID'";
    $result = $conn->query($query);

    $doctors = [];
    while ($row = $result->fetch_assoc()) {
        $doctors[] = $row;
    }
    echo json_encode($doctors);
}

$conn->close();
?>
 
