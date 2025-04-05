<?php
session_start();

// Database connection
$connection = mysqli_connect("localhost", "root", "root", "curego");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($connection, $_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    // Choose the table based on the role (doctor or patient)
    $table = ($role === "doctor") ? "Doctor" : "Patient";
    
    // Query to find the user based on email
    $query = "SELECT * FROM $table WHERE emailAddress = '$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        // Verify the entered password against the hashed password
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_type"] = $role;

            // Redirect to the appropriate homepage based on the role
            $redirect = ($role === "doctor") ? "doctorHomepage.php" : "patientHomepage.php";
            header("Location: $redirect");
            exit();
        }
    }

    // If login fails, redirect back to the login page with an error
    header("Location: login.html?error=invalid");
    exit();
}
?>
