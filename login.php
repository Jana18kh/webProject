<?php

session_start();

// Database connection
$connection = mysqli_connect("sql110.infinityfree.com", "if0_38818376", "zWf9MgaDKqcc", "if0_38818376_curego");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($connection, $_POST["email"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"];

    // Choose the table based on the role (doctor or patient)
    $table = ($role === "doctor") ? "Doctor" : "Patient";

    // Query to find the user based on email
    $stmt = $connection->prepare("SELECT * FROM $table WHERE emailAddress = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the user if found
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify the entered password against the hashed password: 12345678
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
