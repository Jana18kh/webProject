<?php
session_start();

// Check if the user is logged in and if they are a doctor
if (!isset($_SESSION['user_id']) & $_SESSION['role'] == 'doctor') {
    header("Location: homepage.html"); // Redirect to homepage if not a doctor
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

// Get doctor information based on session
$doctor_id = $_SESSION['user_id'];  // Use the user_id session variable
$sql = "SELECT d.*, s.speciality 
        FROM Doctor d
        JOIN Speciality s ON d.SpecialityID = s.id
        WHERE d.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

// Fetch upcoming appointments
$sql_appointments = "SELECT a.*, p.firstName AS patient_firstName, p.lastName AS patient_lastName, 
                             TIMESTAMPDIFF(YEAR, p.DoB, CURDATE()) AS age, p.gender
                     FROM Appointment a
                     JOIN Patient p ON a.PatientID = p.id
                     WHERE a.DoctorID = ? AND (a.status = 'Pending' OR a.status = 'Confirmed')
                     ORDER BY a.date, a.time";
$stmt_appointments = $conn->prepare($sql_appointments);
$stmt_appointments->bind_param("i", $doctor_id);
$stmt_appointments->execute();
$appointments = $stmt_appointments->get_result();

// Fetch patients who have completed appointments, and their medications
$sql_patients = "SELECT DISTINCT p.firstName, p.lastName, TIMESTAMPDIFF(YEAR, p.DoB, CURDATE()) AS age, 
                        p.gender, GROUP_CONCAT(m.MedicationName SEPARATOR ', ') AS medications
                 FROM Patient p
                 JOIN Appointment a ON p.id = a.PatientID
                 LEFT JOIN Prescription pr ON a.id = pr.AppointmentID
                 LEFT JOIN Medication m ON pr.MedicationID = m.id
                 WHERE a.DoctorID = ? AND a.status = 'Done'
                 GROUP BY p.id
                 ORDER BY p.firstName";
$stmt_patients = $conn->prepare($sql_patients);
$stmt_patients->bind_param("i", $doctor_id);
$stmt_patients->execute();
$patients = $stmt_patients->get_result();

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor's Homepage</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
    <header>
        <div class="logo">
            <img src="logo.png" alt="CureGO Logo">
        </div>
        <h1>CureGO</h1>
    </header>

    <div class="container">
        <h2>Welcome Dr. <?php echo $doctor['firstName']; ?></h2>
        <div class="doctorHome">
            <div id="info">
                <p>First name: <b><?php echo $doctor['firstName']; ?></b></p>
                <p>Last name: <b><?php echo $doctor['lastName']; ?></b></p>
                <p>Email Address: <b><?php echo $doctor['emailAddress']; ?></b></p>
                <p>Specialty: <b><?php echo $doctor['speciality']; ?></b></p>
            </div>
            <div class="doctorPhoto">
                <img src="uploads/<?php echo $doctor['uniqueFileName']; ?>" alt="Doctor's Photo">
            </div>
        </div>

        <h3>Upcoming Appointments</h3>
        <table id="appointmentsTable">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Patient's Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Reason for Visit</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($appointments->num_rows > 0) {
                    while($row = $appointments->fetch_assoc()) { ?>
                        <tr id="appointment-<?php echo $row['id']; ?>">
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo $row['time']; ?></td>
                            <td><?php echo $row['patient_firstName'] . " " . $row['patient_lastName']; ?></td>
                            <td><?php echo $row['age']; ?></td>
                            <td><?php echo $row['gender']; ?></td>
                            <td><?php echo $row['reason']; ?></td>
                            <td id="status-<?php echo $row['id']; ?>"><?php echo $row['status']; ?></td>
                            <td>
                                <?php if ($row['status'] == 'Pending') { ?>
                                    <button class="confirm" data-id="<?php echo $row['id']; ?>">Confirm</button>
                                <?php } elseif ($row['status'] == 'Confirmed') { ?>
                                    <a href="prescribemedicationpage.php?id=<?php echo $row['id']; ?>">Prescribe</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } 
                } else { 
                    echo "<tr><td colspan='8'>No upcoming appointments.</td></tr>";
                } ?>
            </tbody>
        </table>

        <h3>Your Patients</h3>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Medications</th>
                </tr>
            </thead>
            <tbody>
                <?php while($patient = $patients->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $patient['firstName'] . " " . $patient['lastName']; ?></td>
                        <td><?php echo $patient['age']; ?></td>
                        <td><?php echo $patient['gender']; ?></td>
                        <td><?php echo $patient['medications']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="signOut">
            <a href="signout.php">Log Out</a>
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
        $(document).ready(function() {
    $(".confirm").click(function() {
        var appointmentId = $(this).data("id");

        // Send AJAX request to update the status
        $.ajax({
            url: "confirmAppointmentStatus.php",
            type: "POST",
            data: { id: appointmentId },
            success: function(response) {
                if (response == "true") {
                    // Update the status text and change the button to Prescribe
                    $("#status-" + appointmentId).text("Confirmed");

                    // Replace Confirm button with Prescribe button
                    var prescribeButton = '<a href="prescribemedicationpage.php?id=' + appointmentId + '">Prescribe</a>';
                    $("#appointment-" + appointmentId + " td:last-child").html(prescribeButton);
                } else {
                    alert("Failed to confirm appointment.");
                }
            }
        });
    });
});

    </script>
</body>
</html>
