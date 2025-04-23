<?php
session_start();

// Check if the user is logged in as a patient
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
    <style>
        /* Add some basic styling for notifications */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px;
            border-radius: 5px;
            color: white;
            display: none;
            z-index: 1000;
        }
        .success {
            background-color: #4CAF50;
        }
        .error {
            background-color: #f44336;
        }
    </style>
</head>
<body>
    <!-- Notification divs -->
    <div id="success-notification" class="notification success"></div>
    <div id="error-notification" class="notification error"></div>

    <header>
        <div class="logo">
            <img src="logo.png" alt="CureGO Logo">
        </div>
        <h1>CureGO</h1>
    </header>

    <div class="container">
        <h2>Welcome <?php echo htmlspecialchars($patient['firstName']); ?></h2>
        <div class="patient-info">
            <p>First name: <b><?php echo htmlspecialchars($patient['firstName']); ?></b></p>
            <p>Last name: <b><?php echo htmlspecialchars($patient['lastName']); ?></b></p>
            <p>Email Address: <b><?php echo htmlspecialchars($patient['emailAddress']); ?></b></p>
            <p>Date of Birth: <b><?php echo date('F j, Y', strtotime($patient['DoB'])); ?></b></p>
            <p>Age: <b><?php echo $age; ?></b></p>
            <p>Gender: <b><?php echo htmlspecialchars($patient['gender']); ?></b></p>
        </div>

        <a href="appointmentBooking.php" class="book-btn">Book an appointment</a>

        <h3>Upcoming Appointments</h3>
        <table id="appointments-table">
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
                <?php if ($appointments->num_rows > 0): ?>
                    <?php while($row = $appointments->fetch_assoc()): ?>
                        <tr data-appointment-id="<?php echo $row['id']; ?>">
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo substr($row['time'], 0, 5); ?></td>
                            <td><?php echo htmlspecialchars($row['doctor_firstName'] . " " . $row['doctor_lastName']); ?></td>
                            <td class="doctorPhoto">
                                <img src="uploads/<?php echo htmlspecialchars($row['doctor_photo']); ?>" alt="Doctor's Photo">
                            </td>
                            <td><?php echo htmlspecialchars($row['speciality']); ?></td>
                            <td><?php echo htmlspecialchars($row['reason']); ?></td>
                            <td><?php echo htmlspecialchars($row['status']); ?></td>
                            <td>
                                <?php if ($row['status'] == 'Pending' || $row['status'] == 'Confirmed'): ?>
                                    <a href="#" class="cancel-btn" onclick="cancelAppointment(<?php echo $row['id']; ?>); return false;">Cancel</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No upcoming appointments.</td>
                    </tr>
                <?php endif; ?>
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
                <?php if ($history->num_rows > 0): ?>
                    <?php while($row = $history->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo substr($row['time'], 0, 5); ?></td>
                            <td><?php echo htmlspecialchars($row['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['speciality']); ?></td>
                            <td><?php echo $row['medications'] ? htmlspecialchars($row['medications']) : 'None'; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">No appointment history.</td>
                    </tr>
                <?php endif; ?>
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
        function showNotification(type, message) {
            const notification = document.getElementById(`${type}-notification`);
            notification.textContent = message;
            notification.style.display = 'block';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        function cancelAppointment(appointmentId) {
            if (confirm('Are you sure you want to cancel this appointment?')) {
                // Show loading state
                const cancelBtn = document.querySelector(`tr[data-appointment-id="${appointmentId}"] .cancel-btn`);
                if (cancelBtn) {
                    cancelBtn.textContent = 'Cancelling...';
                    cancelBtn.style.pointerEvents = 'none';
                }
                
                // Create a form data object
                const formData = new FormData();
                formData.append('id', appointmentId);
                
                // Send AJAX request
                fetch('cancelappointment.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Find and remove the table row
                        const row = document.querySelector(`tr[data-appointment-id="${appointmentId}"]`);
                        if (row) {
                            row.remove();
                        }
                        
                        // Check if table is empty now
                        const tbody = document.querySelector('#appointments-table tbody');
                        if (tbody.children.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="8">No upcoming appointments.</td></tr>';
                        }
                        
                        showNotification('success', 'Appointment cancelled successfully');
                    } else {
                        showNotification('error', 'Failed to cancel appointment: ' + (data.error || 'Unknown error'));
                        if (cancelBtn) {
                            cancelBtn.textContent = 'Cancel';
                            cancelBtn.style.pointerEvents = 'auto';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('error', 'An error occurred while cancelling the appointment');
                    if (cancelBtn) {
                        cancelBtn.textContent = 'Cancel';
                        cancelBtn.style.pointerEvents = 'auto';
                    }
                });
            }
        }
    </script>
</body>
</html>
