<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: plogin.php");
    exit();
}

// Database connection parameters
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email = $_SESSION['email'];

// View Appointments functionality
$sql_patient = "SELECT Pid FROM Patient WHERE Email = '$email'";
$result_patient = $conn->query($sql_patient);

if ($result_patient->num_rows > 0) {
    $row = $result_patient->fetch_assoc();
    $pid = $row['Pid'];

    $sql_appointments = "SELECT * FROM Appointment WHERE Pid = '$pid'";
    $result_appointments = $conn->query($sql_appointments);
    $appointments = [];
    if ($result_appointments->num_rows > 0) {
        while ($row = $result_appointments->fetch_assoc()) {
            $appointments[] = $row;
        }
    } else {
        echo "No appointments found.";
    }
} else {
    echo "Error: Patient not found.";
}

// View Doctors functionality
$sql_doctors = "SELECT Email, Specialisation FROM Doctor";
$result_doctors = $conn->query($sql_doctors);
$doctors = [];
if ($result_doctors->num_rows > 0) {
    while ($row = $result_doctors->fetch_assoc()) {
        $doctors[] = $row;
    }
} else {
    echo "No doctors found.";
}

// Book Appointment functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['book_appointment'])) {
    $doctor_email = $_POST['doctor_email'];
    $appdate = $_POST['appdate'];
    $apptime = $_POST['apptime'];
    $disease = $_POST['disease'];
    $mode = $_POST['mode'];
    $status = "Pending";

    // Retrieve the Pid of the patient
    $sql_patient = "SELECT Pid FROM Patient WHERE Email = '$email'";
    $result_patient = $conn->query($sql_patient);
    
    if ($result_patient->num_rows > 0) {
        $row = $result_patient->fetch_assoc();
        $pid = $row['Pid'];

        // Insert appointment details
        $sql_book_appointment = "INSERT INTO Appointment (Pid, Email, Appdate, Apptime, Disease, Mode, Status)
                VALUES ('$pid', '$doctor_email', '$appdate', '$apptime', '$disease', '$mode', '$status')";
        
        if ($conn->query($sql_book_appointment) === TRUE) {
            header("Location: ppanel.php");
            exit();
        } else {
            echo "Error: " . $sql_book_appointment . "<br>" . $conn->error;
        }
    } else {
        echo "Error: Patient not found.";
    }
}

// Give Review functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['give_review'])) {
    $ratings = $_POST['ratings'];
    $remarks = $_POST['remarks'];

    // Retrieve the Pid of the patient
    $sql_patient = "SELECT Pid FROM Patient WHERE Email = '$email'";
    $result_patient = $conn->query($sql_patient);
    
    if ($result_patient->num_rows > 0) {
        $row = $result_patient->fetch_assoc();
        $pid = $row['Pid'];

        // Retrieve the doctor's email
        $doctor_email = $_POST['doctor_email'];

        // Insert review details
        $sql_give_review = "INSERT INTO Review (Pid, Email, Ratings, Remarks)
                VALUES ('$pid', '$doctor_email', $ratings, '$remarks')";
        
        if ($conn->query($sql_give_review) === TRUE) {
            header("Location: ppanel.php");
            exit();
        } else {
            echo "Error: " . $sql_give_review . "<br>" . $conn->error;
        }
    } else {
        echo "Error: Patient not found.";
    }
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="#view_appointments">View Appointments</a></li>
            <li><a href="#view_doctors">View Doctors</a></li>
            <li><a href="#book_appointment">Book Appointment</a></li>
            <li><a href="#give_review">Give Review</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>Welcome, <?php echo $_SESSION['email']; ?></h1>

        <!-- View Appointments -->
        <div id="view_appointments" class="panel">
            <h2>View Appointments</h2>
            <?php if (!empty($appointments)) : ?>
                <ul>
                    <?php foreach ($appointments as $appointment) : ?>
                        <li>Appointment ID: <?php echo $appointment['AppID']; ?> - Date: <?php echo $appointment['Appdate']; ?> - Time: <?php echo $appointment['Apptime']; ?> - Status: <?php echo $appointment['Status']; ?> - Prescription: <?php echo $appointment['Prescription']; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No appointments found.</p>
            <?php endif; ?>
        </div>

        <!-- View Doctors -->
        <div id="view_doctors" class="panel">
            <h2>View Doctors</h2>
            <?php if (!empty($doctors)) : ?>
                <ul>
                    <?php foreach ($doctors as $doctor) : ?>
                        <li>Email: <?php echo $doctor['Email']; ?> - Specialisation: <?php echo $doctor['Specialisation']; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No doctors found.</p>
            <?php endif; ?>
        </div>

        <!-- Book Appointment -->
        <div id="book_appointment" class="panel">
            <h2>Book Appointment</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="doctor_email">Select Doctor:</label>
                    <select name="doctor_email" id="doctor_email" required>
                        <?php 
                        foreach ($doctors as $doctor) {
                            echo "<option value='" . $doctor['Email'] . "'>" . $doctor['Email'] . " - " . $doctor['Specialisation'] . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="appdate">Appointment Date:</label>
                    <input type="date" name="appdate" id="appdate" required>
                </div>
                <div class="form-group">
                    <label for="apptime">Appointment Time:</label>
                    <input type="time" name="apptime" id="apptime" required>
                </div>
                <div class="form-group">
                    <label for="disease">Disease:</label>
                    <input type="text" name="disease" id="disease" required>
                </div>
                <div class="form-group">
                    <label for="mode">Mode:</label>
                    <select name="mode" id="mode" required>
                        <option value="online">Online</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
                <input type="submit" name="book_appointment" value="Book Appointment">
            </form>
        </div>

        <!-- Give Review -->
        <?php if (!empty($doctors)) : ?>
        <div id="give_review" class="panel">
            <h2>Give Review</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="doctor_email_review">Doctor Email:</label>
                    <select name="doctor_email" id="doctor_email_review">
                        <?php foreach ($doctors as $doctor) : ?>
                            <option value="<?php echo $doctor['Email']; ?>"><?php echo $doctor['Email']; ?> - <?php echo $doctor['Specialisation']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="ratings">Ratings:</label>
                    <input type="number" name="ratings" id="ratings" min="1" max="5" required>
                </div>
                <div class="form-group">
                    <label for="remarks">Remarks:</label>
                    <textarea name="remarks" id="remarks" rows="4" cols="50" required></textarea>
                </div>
                <input type="submit" name="give_review" value="Give Review">
            </form>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
