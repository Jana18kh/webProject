<?php
// Start the session and database connection
session_start();

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "curego";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the appointment ID is passed in the URL
if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];

    // Fetch the appointment and patient details based on the appointment ID
    $sql_appointment = "SELECT a.*, p.firstName AS patient_firstName, p.lastName AS patient_lastName, 
                        TIMESTAMPDIFF(YEAR, p.DoB, CURDATE()) AS age, p.gender
                        FROM Appointment a
                        JOIN Patient p ON a.PatientID = p.id
                        WHERE a.id = ?";
    $stmt = $conn->prepare($sql_appointment);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the appointment exists
    if ($result->num_rows > 0) {
        $appointment = $result->fetch_assoc();
        $patient_name = $appointment['patient_firstName'] . " " . $appointment['patient_lastName'];
        $age = $appointment['age'];
        $gender = $appointment['gender'];
    } else {
        echo "Appointment not found!";
        exit();
    }
} else {
    echo "Appointment ID is missing!";
    exit();
}

// Handle form submission for prescribing medication
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $medications = isset($_POST['medications']) ? $_POST['medications'] : [];

    // Insert the prescribed medications into the Prescription table
    foreach ($medications as $medication) {
        $sql = "SELECT id FROM Medication WHERE MedicationName = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $medication);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $medication_id = $result->fetch_assoc()['id'];

            // Insert into the Prescription table
            $sql_insert = "INSERT INTO Prescription (AppointmentID, MedicationID) VALUES (?, ?)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bind_param("ii", $appointment_id, $medication_id);
            $stmt_insert->execute();
        }
    }

    // Redirect back to the doctor homepage after submitting the form
    header("Location: doctorHomepage.php");
    exit();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="style.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Patient's Medications</title>
  </head>
  <body>
    <header>
      <div class="logo">
        <img src="logo.png" alt="CureGO Logo" />
      </div>
      <h1>CureGO</h1>
    </header>
    <div class="container">
      <h2>Patient's Medications</h2>
      <form action="prescribeMedicationPage.php?id=<?php echo $_GET['id']; ?>" method="POST">
        <div class="row">
          <div class="group">
            <label for="patientName">Patient's Name:</label>
            <input
              type="text"
              id="patientName"
              name="patientName"
              value="<?php echo $patient_name; ?>"
              readonly
            />
          </div>
          <div class="group">
            <label for="age">Age:</label>
            <input type="number" name="age" value="<?php echo $age; ?>" min="0" readonly />
          </div>
        </div>
        <div>
          <label for="genderOptions">Gender: </label>
          <div class="row">
            <div class="group">
              <input type="radio" name="genderOptions" value="Female" <?php echo ($gender == 'Female') ? 'checked' : ''; ?> />
              <label for="female">Female</label>
            </div>
            <div class="group">
              <input type="radio" name="genderOptions" value="Male" <?php echo ($gender == 'Male') ? 'checked' : ''; ?> />
              <label for="male">Male</label>
            </div>
          </div>
        </div>

        <label for="medications">Medications: </label>
        <div class="row">
          <label><input type="checkbox" name="medications[]" value="Aspirin" />Aspirin</label>
          <label><input type="checkbox" name="medications[]" value="Ibuprofen" />Ibuprofen</label>
          <label><input type="checkbox" name="medications[]" value="Paracetamol" />Paracetamol</label>
          <label><input type="checkbox" name="medications[]" value="Antibiotics" />Antibiotics</label>
        </div>

        <div class="centerButton">
          <button class="button" type="submit">Submit</button>
        </div>
      </form>
    </div>
    <footer>
      <div class="footerContainer">
        <div class="contact">
          <div class="contactItems">
            <div class="contactItem">
              <img src="phone.png" alt="Phone Icon" />
              <span>+966111111</span>
            </div>
            <div class="contactItem">
              <img src="telephone2.png" alt="Mobile Icon" />
              <span>+01134657</span>
            </div>
            <div class="contactItem">
              <img src="Email.png" alt="Email Icon" />
              <span>CureGo@gmail.com</span>
            </div>
          </div>
        </div>
        <p>Copyright &copy; 2025 CureGo. All rights reserved.</p>
      </div>
    </footer>
  </body>
</html>

