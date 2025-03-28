<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "CureGO");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($connection, $_POST["email"]);
    $password = $_POST["password"];
    $role = $_POST["role"];

    $table = ($role === "doctor") ? "doctor" : "patient";
    $query = "SELECT * FROM $table WHERE emailAddress='$email' LIMIT 1";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        //security part 
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_type"] = $role;

            $redirect = ($role === "doctor") ? "doctorHomepage.php" : "patientHomepage.php";
            header("Location: $redirect");
            exit();
        }
    }
    header("Location: login.html?error=invalid");
    exit();
}
?>
