<?php
session_start();

// Check if the user is logged in as a patient
if (!isset($_SESSION['user_id'])) {
    header("Location: homepage.html"); // Redirect to homepage if not logged in
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "curego";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get patient information based on session
$patient_id = $_SESSION['user_id'];
$sql = "SELECT * FROM Patient WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

// Calculate age from date of birth
$age = date_diff(date_create($patient['DoB']), date_create('today'))->y;

// Fetch upcoming appointments
$sql_appointments = "SELECT a.*, d.firstName AS doctor_firstName, d.lastName AS doctor_lastName, 
                            d.uniqueFileName AS doctor_photo, s.speciality
                     FROM Appointment a
                     JOIN Doctor d ON a.DoctorID = d.id
                     JOIN Speciality s ON d.SpecialityID = s.id
                     WHERE a.PatientID = ? AND (a.status = 'Pending' OR a.status = 'Confirmed')
                     ORDER BY a.date, a.time";
$stmt_appointments = $conn->prepare($sql_appointments);
$stmt_appointments->bind_param("i", $patient_id);
$stmt_appointments->execute();
$appointments = $stmt_appointments->get_result();

// Fetch completed appointments with prescriptions
$sql_history = "SELECT a.date, a.time, CONCAT(d.firstName, ' ', d.lastName) AS doctor_name,
                       s.speciality, GROUP_CONCAT(m.MedicationName SEPARATOR ', ') AS medications
                FROM Appointment a
                JOIN Doctor d ON a.DoctorID = d.id
                JOIN Speciality s ON d.SpecialityID = s.id
                LEFT JOIN Prescription p ON a.id = p.AppointmentID
                LEFT JOIN Medication m ON p.MedicationID = m.id
                WHERE a.PatientID = ? AND a.status = 'Done'
                GROUP BY a.id
                ORDER BY a.date DESC";
$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param("i", $patient_id);
$stmt_history->execute();
$history = $stmt_history->get_result();

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient's Homepage</title>
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
        <h2>Welcome <?php echo $patient['firstName']; ?></h2>
        <div class="patient-info">
            <p>First name: <b><?php echo $patient['firstName']; ?></b></p>
            <p>Last name: <b><?php echo $patient['lastName']; ?></b></p>
            <p>Email Address: <b><?php echo $patient['emailAddress']; ?></b></p>
            <p>Date of Birth: <b><?php echo date('F j, Y', strtotime($patient['DoB'])); ?></b></p>
            <p>Age: <b><?php echo $age; ?></b></p>
            <p>Gender: <b><?php echo $patient['gender']; ?></b></p>
        </div>

        <a href="appointmentBooking.php" class="book-btn">Book an appointment</a>

        <h3>Upcoming Appointments</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor's Name</th>
                    <th>Doctor's Photo</th>
                    <th>Specialty</th>
                    <th>Reason for Visit</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($appointments->num_rows > 0) {
                    while($row = $appointments->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo substr($row['time'], 0, 5); ?></td>
                            <td><?php echo $row['doctor_firstName'] . " " . $row['doctor_lastName']; ?></td>
                            <td class="doctorPhoto">
                                <img src="uploads/<?php echo $row['doctor_photo']; ?>" alt="Doctor's Photo">
                            </td>
                            <td><?php echo $row['speciality']; ?></td>
                            <td><?php echo $row['reason']; ?></td>
                            <td><?php echo $row['status']; ?></td>
                            <td>
                                <?php if ($row['status'] == 'Pending' || $row['status'] == 'Confirmed') { ?>
                                    <a href="cancelappointment.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to cancel this appointment?')">Cancel</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } 
                } else { 
                    echo "<tr><td colspan='8'>No upcoming appointments.</td></tr>";
                } ?>
            </tbody>
        </table>

        <h3>Appointment History</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Doctor's Name</th>
                    <th>Specialty</th>
                    <th>Medications Prescribed</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($history->num_rows > 0) {
                    while($row = $history->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['date']; ?></td>
                            <td><?php echo substr($row['time'], 0, 5); ?></td>
                            <td><?php echo $row['doctor_name']; ?></td>
                            <td><?php echo $row['speciality']; ?></td>
                            <td><?php echo $row['medications'] ? $row['medications'] : 'None'; ?></td>
                        </tr>
                    <?php } 
                } else { 
                    echo "<tr><td colspan='5'>No appointment history.</td></tr>";
                } ?>
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
</body>
</html>
