<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: homepage.html");
    exit();
}

$servername = "sql110.infinityfree.com";  // Hostname
$username = "if0_38818376";              // Username
$password = "zWf9MgaDKqcc";               // Password
$dbname = "if0_38818376_curego";         // Database name


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['doctor'])) {
    $patient_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doctor'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = $_POST['reason'];

    if (empty($doctor_id) || empty($date) || empty($time) || empty($reason)) {
        $error_message = "All fields are required!";
    } else {
        $check_sql = "SELECT id FROM Appointment WHERE DoctorID = ? AND date = ? AND time = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("iss", $doctor_id, $date, $time);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $error_message = "The selected time slot is already booked. Please choose another time.";
        } else {
            $insert_sql = "INSERT INTO Appointment (PatientID, DoctorID, date, time, reason, status) 
                           VALUES (?, ?, ?, ?, ?, 'Pending')";
            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param("iisss", $patient_id, $doctor_id, $date, $time, $reason);

            if ($insert_stmt->execute()) {
                $success_message = "Appointment booked successfully!";
                $_POST = array();
            } else {
                $error_message = "Error booking appointment. Please try again.";
            }
        }
    }
}

$specialties = mysqli_query($conn, "SELECT * FROM Speciality");

$time_slots = [];
for ($hour = 9; $hour <= 16; $hour++) {
    $time_slots[] = sprintf("%02d:00", $hour);
    $time_slots[] = sprintf("%02d:30", $hour);
}
$time_slots[] = "17:00";

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment | CureGO</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="CureGO Logo">
        </div>
        <h1>CureGO</h1>
    </header>

    <div class="container">
        <h2>Book a New Appointment</h2>
        
        <?php if ($success_message): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="booking-form">
            <form method="POST" action="appointmentBooking.php">
                <div class="form-group">
                    <label for="speciality">Select Speciality:</label>
                    <select id="speciality" required>
                        <option value="">-- Select Speciality --</option>
                        <?php while($row = mysqli_fetch_assoc($specialties)): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['speciality']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="doctor">Select Doctor:</label>
                    <select id="doctor" name="doctor" required>
                        <option value="">-- Select a Doctor --</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="date">Appointment Date:</label>
                    <input type="date" id="date" name="date" 
                           min="<?php echo date('Y-m-d'); ?>" 
                           value="<?php echo isset($_POST['date']) ? $_POST['date'] : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="time">Appointment Time:</label>
                    <select id="time" name="time" required>
                        <option value="">-- Select a Time --</option>
                        <?php foreach ($time_slots as $slot): ?>
                            <option value="<?php echo $slot; ?>" 
                                <?php if (isset($_POST['time']) && $_POST['time'] == $slot) echo 'selected'; ?>>
                                <?php echo $slot; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="reason">Reason for Visit:</label>
                    <textarea id="reason" name="reason" required><?php echo isset($_POST['reason']) ? htmlspecialchars($_POST['reason']) : ''; ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="submit-btn">Book Appointment</button>
                    <a href="patientHomepage.php" class="cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <div class="footerContainer">
            <div class="contact">
                <div class="contactItems">
                    <div class="contactItem">
                        <img src="phone.png" alt="Phone Icon">
                        <span>+966111111</span>
                    </div>
                    <div class="contactItem">
                        <img src="telephone2.png" alt="Mobile Icon">
                        <span>+01134657</span>
                    </div>
                    <div class="contactItem">
                        <img src="Email.png" alt="Email Icon">
                        <span>CureGo@gmail.com</span>
                    </div>
                </div>
            </div>
            <p>&copy; 2025 CureGo. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function(){
        $('#speciality').change(function(){
            var specialityID = $(this).val();
            $('#doctor').empty().append('<option value="">-- Select a Doctor --</option>');
            if (specialityID) {
                $.ajax({
                    url: 'getDoctorsBySpeciality.php',
                    type: 'POST',
                    data: { speciality_id: specialityID },
                    dataType: 'json',
                    success: function(data){
                        $.each(data, function(index, doctor){
                            $('#doctor').append('<option value="'+doctor.id+'">Dr. ' + doctor.name + '</option>');
                        });
                    }
                });
            }
        });
    });
    </script>
</body>
</html>


