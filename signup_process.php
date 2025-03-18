<?php
session_start();
$connection = mysqli_connect("localhost", "root", "", "CureGO");
if (mysqli_connect_error()) {
    die("خطأ في الاتصال: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["userType"])) {
    $userType = $_POST["userType"];
    $firstName = mysqli_real_escape_string($connection, $_POST["firstName"]);
    $lastName = mysqli_real_escape_string($connection, $_POST["lastName"]);
    $id = mysqli_real_escape_string($connection, $_POST["id"]);
    $email = mysqli_real_escape_string($connection, $_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $uniqueFileName = "";

    $checkTable = ($userType == "Patient") ? "Patient" : "Doctor";
    $checkEmail = "SELECT emailAddress FROM $checkTable WHERE emailAddress='$email'";
    $result = mysqli_query($connection, $checkEmail);
    if (mysqli_num_rows($result) > 0) {
        header("Location: signup.php?error=البريد+الإلكتروني+موجود+بالفعل!");
        exit();
    }

    if ($userType == "Doctor" && isset($_FILES["photo"])) {
        $file = $_FILES["photo"];
        $fileExt = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $uniqueFileName = uniqid() . '.' . $fileExt;

        $uploadDir = "uploads/";
        $uploadPath = $uploadDir . $uniqueFileName;

        $allowedExt = array("jpg", "jpeg", "png");
        if (!in_array($fileExt, $allowedExt)) {
            header("Location: signup.php?error=نوع+الملف+غير+مسموح+به!");
            exit();
        } elseif ($file["error"] === UPLOAD_ERR_OK) {
            if (!move_uploaded_file($file["tmp_name"], $uploadPath)) {
                header("Location: signup.php?error=فشل+في+رفع+الملف!");
                exit();
            }
        } else {
            header("Location: signup.php?error=خطأ+في+رفع+الملف!");
            exit();
        }
    }

    if ($userType == "Patient") {
        $gender = mysqli_real_escape_string($connection, $_POST["gender"]);
        $dob = mysqli_real_escape_string($connection, $_POST["dob"]);
        $sql = "INSERT INTO Patient (firstName, lastName, Gender, DoB, emailAddress, password) VALUES ('$firstName', '$lastName', '$gender', '$dob', '$email', '$password')";
    } else {
        $speciality = mysqli_real_escape_string($connection, $_POST["speciality"]);
        $sql = "INSERT INTO Doctor (firstName, lastName, uniqueFileName, SpecialityID, emailAddress, password) VALUES ('$firstName', '$lastName', '$uniqueFileName', (SELECT id FROM Speciality WHERE speciality='$speciality'), '$email', '$password')";
    }

    if (mysqli_query($connection, $sql)) {
        $userId = mysqli_insert_id($connection);
        $_SESSION["user_id"] = $userId;
        $_SESSION["user_type"] = $userType;
        $redirect = ($userType == "Patient") ? "patientHomepage.php" : "doctorHomepage.php";
        header("Location: $redirect");
        exit();
    } else {
        header("Location: signup.php?error=خطأ+في+التسجيل:+" . urlencode(mysqli_error($connection)));
        exit();
    }
}
?>