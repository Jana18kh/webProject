<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$message = "";  // To store any error or success message

// Database connection (direct in this file)
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "curego";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Getting form data
    $userType = $_POST["userType"];  // To differentiate between patient and doctor
    $firstName = trim($_POST["firstName"]);
    $lastName = trim($_POST["lastName"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Validate input
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $message = "All fields are required.";
        echo $message;
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        echo $message;
        exit();
    } else {
        // Check if email already exists in Patient or Doctor table based on userType
        $checkTable = ($userType == "Patient") ? "Patient" : "Doctor";
        $stmt = $conn->prepare("SELECT emailAddress FROM $checkTable WHERE emailAddress = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "Email is already registered.";
            echo $message;
            exit();
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user (Doctor or Patient)
            if ($userType == "Patient") {
                // Insert into Patient table
                $gender = $_POST["gender"];
                $dob = $_POST["dob"];
                $stmt = $conn->prepare("INSERT INTO Patient (firstName, lastName, Gender, DoB, emailAddress, password) 
                                        VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $firstName, $lastName, $gender, $dob, $email, $hashedPassword);
            } else {
                // Handle file upload for doctor photo
                $photoUploaded = false;
                if (isset($_FILES['photoDoctor']) && $_FILES['photoDoctor']['error'] === UPLOAD_ERR_OK) {
                    $fileTmpPath = $_FILES['photoDoctor']['tmp_name'];
                    $fileName = $_FILES['photoDoctor']['name'];
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                    $newFileName = "dr." . $firstName . "_" . $lastName . "." . $fileExtension;
                    $uploadPath = 'uploads/' . $newFileName;

                    // Move file to the uploads directory
                    if (move_uploaded_file($fileTmpPath, $uploadPath)) {
                        $photoUploaded = true;
                    } else {
                        $message = "Photo upload failed. Please try again.";
                        echo $message;
                        exit();
                    }
                }

                // Insert into Doctor table if photo upload is successful
                if ($photoUploaded) {
                    $specialty = $_POST["speciality"];
                    $stmt = $conn->prepare("INSERT INTO Doctor (firstName, lastName, uniqueFileName, SpecialityID, emailAddress, password) 
                                            VALUES (?, ?, ?, (SELECT id FROM Speciality WHERE speciality=?), ?, ?)");
                    $stmt->bind_param("ssssss", $firstName, $lastName, $newFileName, $specialty, $email, $hashedPassword);
                } else {
                    $message = "Photo upload failed. Please try again.";
                    echo $message;
                    exit();
                }
            }

            // Execute query
            if ($stmt->execute()) {
                // Get user ID and set session variables
                $userId = $conn->insert_id;
                $_SESSION["user_id"] = $userId;
                $_SESSION["user_type"] = $userType;

                // Redirect to the appropriate homepage (Doctor or Patient)
                $redirect = ($userType == "Patient") ? "patientHomepage.php" : "doctorHomepage.php";
                header("Location: $redirect");
                exit();
            } else {
                $message = "Registration failed. Please try again.";
                echo $message;
                exit();
            }
        }

        $stmt->close();
    }
    $conn->close();
}
?>
