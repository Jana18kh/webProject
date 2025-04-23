<?php
session_start();

// Check if user is logged in as a patient
if (!isset($_SESSION['user_id'])) {
    header("Location: homepage.html");
    exit();
}

// Database connection
$servername = "sql110.infinityfree.com";
$username = "if0_38818376";
$password = "VZrJJdHAmkIV";
$dbname = "if0_38818376_curego";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_SESSION['user_id'];
    $doctor_id = $_POST['doctor'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $reason = $_POST['reason'];
    
    // Validate inputs
    if (empty($doctor_id) || empty($date) || empty($time) || empty($reason)) {
        $error_message = "All fields are required!";
    } else {
        // Check if the selected time slot is available
        $check_sql = "SELECT id FROM Appointment WHERE DoctorID = ? AND date = ? AND time = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("iss", $doctor_id, $date, $time);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = "The selected time slot is already booked. Please choose another time.";
        } else {
            // Insert the new appointment
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

// Get list of doctors with their specialties
$doctors_sql = "SELECT d.id, d.firstName, d.lastName, s.speciality 
               FROM Doctor d 
               JOIN Speciality s ON d.SpecialityID = s.id
               ORDER BY d.lastName, d.firstName";
$doctors_result = $conn->query($doctors_sql);

// Generate time slots (every 30 minutes from 9AM to 5PM)
$time_slots = array();
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
                    <label for="doctor">Select Doctor:</label>
                    <select id="doctor" name="doctor" required>
                        <option value="">-- Select a Doctor --</option>
                        <?php while ($doctor = $doctors_result->fetch_assoc()): ?>
                            <option value="<?php echo $doctor['id']; ?>" 
                                <?php if (isset($_POST['doctor']) && $_POST['doctor'] == $doctor['id']) echo 'selected'; ?>>
                                Dr. <?php echo $doctor['firstName'] . ' ' . $doctor['lastName']; ?> (<?php echo $doctor['speciality']; ?>)
                            </option>
                        <?php endwhile; ?>
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

    <script>
        // Set minimum date to today
        document.getElementById('date').min = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>
