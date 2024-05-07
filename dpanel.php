<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: dregistration.php");
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
$sql_appointments = "SELECT * FROM Appointment WHERE Email = '$email'";
$result_appointments = $conn->query($sql_appointments);
$appointments = [];
if ($result_appointments->num_rows > 0) {
    while ($row = $result_appointments->fetch_assoc()) {
        $appointments[] = $row;
    }
} else {
    echo "No appointments found.";
}

// Prescribe Appointment functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['prescribe'])) {
    $app_id = $_POST['app_id'];
    $prescription = $_POST['prescription'];

    $sql_prescribe = "UPDATE Appointment SET Prescription = '$prescription' WHERE AppID = $app_id";
    if ($conn->query($sql_prescribe) === TRUE) {
        // Prescription updated successfully
    } else {
        echo "Error updating prescription: " . $conn->error;
    }
}

// Update Doctor Info functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_info'])) {
    $new_specialisation = $_POST['new_specialisation'];
    $new_fees = $_POST['new_fees'];

    $sql_update = "UPDATE Doctor SET Specialisation = '$new_specialisation', Fees = '$new_fees' WHERE Email = '$email'";
    if ($conn->query($sql_update) === TRUE) {
        // Doctor info updated successfully
    } else {
        echo "Error updating doctor info: " . $conn->error;
    }
}

//fetch the staff form the staff table 
$sql_staff = "SELECT sid, name FROM Staff";
$result_staff = $conn->query($sql_staff);
$staff_options = "";
if ($result_staff->num_rows > 0) {
    while ($row_staff = $result_staff->fetch_assoc()) {
        $staff_options .= "<option value='" . $row_staff['sid'] . "'>" . $row_staff['name'] . "</option>";
    }
} else {
    $staff_options = "<option value=''>No staff available</option>";
}

// Allocate Rooms to Staff functionality
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['allocate_rooms'])) {
    $staff_id = $_POST['staff_id'];
    $room_no = $_POST['room_no'];

    $sql_allocate = "INSERT INTO Room (room_no, sid) VALUES ('$room_no', '$staff_id')";
    if ($conn->query($sql_allocate) === TRUE) {
        // Room allocated to staff successfully
    } else {
        echo "Error allocating room: " . $conn->error;
    }
}

// View Patients functionality
$sql_patients = "SELECT * FROM Patient";
$result_patients = $conn->query($sql_patients);
$patients = [];
if ($result_patients->num_rows > 0) {
    while ($row = $result_patients->fetch_assoc()) {
        $patients[] = $row;
    }
} else {
    echo "No patients found.";
}

// View Reviews functionality
$sql_reviews = "SELECT * FROM Review WHERE Email = '$email'";
$result_reviews = $conn->query($sql_reviews);
$reviews = [];
if ($result_reviews->num_rows > 0) {
    while ($row = $result_reviews->fetch_assoc()) {
        $reviews[] = $row;
    }
} else {
    echo "No reviews found.";
}

// Close connection

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <ul>
            <li><a href="#view_appointments">View Appointments</a></li>
            <li><a href="#view_patients">View Patients</a></li>
            <li><a href="#view_reviews">View Reviews</a></li>
            <li><a href="#prescribe_appointment">Prescribe Appointment</a></li>
            <li><a href="#update_info">Update Info</a></li>
            <li><a href="#allocate_rooms">Allocate Rooms to Staff</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <div class="container">
        <h1>Welcome, <?php echo $email; ?></h1>

        <!-- View Appointments -->
        <div id="view_appointments" class="panel">
            <h2>View Appointments</h2>
            <?php if (!empty($appointments)) : ?>
                <ul>
                    <?php foreach ($appointments as $appointment) : ?>
                        <li>Appointment ID: <?php echo $appointment['AppID']; ?> - Date: <?php echo $appointment['Appdate']; ?> - Time: <?php echo $appointment['Apptime']; ?> - Disease: <?php echo $appointment['Disease']; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No appointments found.</p>
            <?php endif; ?>
        </div>

        <!-- Prescribe Appointment -->
        <div id="prescribe_appointment" class="panel">
            <h2>Prescribe Appointment</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="app_id">Appointment ID:</label>
                    <input type="number" name="app_id" id="app_id" required>
                </div>
                <div class="form-group">
                    <label for="prescription">Prescription:</label>
                    <textarea name="prescription" id="prescription" rows="4" cols="50" required></textarea>
                </div>
                <input type="submit" name="prescribe" value="Prescribe">
            </form>
        </div>

        <!-- Update Doctor Info -->
        <div id="update_info" class="panel">
            <h2>Update Doctor Info</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="new_specialisation">New Specialisation:</label>
                    <input type="text" name="new_specialisation" id="new_specialisation" required>
                </div>
                <div class="form-group">
                    <label for="new_fees">New Fees:</label>
                    <input type="number" name="new_fees" id="new_fees" required>
                </div>
                <input type="submit" name="update_info" value="Update Info">
            </form>
        </div>

       <!-- Allocate Rooms to Staff -->
        <div id="allocate_rooms" class="panel">
            <h2>Allocate Rooms to Staff</h2>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="staff_name">Staff Name:</label>
                    <select name="staff_id" id="staff_id" required>
                        <?php echo $staff_options; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="room_no">Room Number:</label>
                    <input type="number" name="room_no" id="room_no" required>
                </div>
                <input type="submit" name="allocate_rooms" value="Allocate Rooms">
            </form>
        </div>

        <!-- View Patients -->
        <div id="view_patients" class="panel">
            <h2>View Patients</h2>
            <?php if (!empty($patients)) : ?>
                <ul>
                    <?php foreach ($patients as $patient) : ?>
                        <li>Patient ID: <?php echo $patient['Pid']; ?> - Name: <?php echo $patient['Fname'] . ' ' . $patient['Lname']; ?> - Gender: <?php echo $patient['Gender']; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p>No patients found.</p>
            <?php endif; ?>
        </div>

        <!-- View Reviews -->
            <div id="view_reviews" class="panel">
                <h2>View Reviews</h2>
                <?php
                    // Fetch reviews based on the doctor's email from the session
                    $reviews_query = "SELECT * FROM Review WHERE Email = '$email'";
                    $reviews_result = mysqli_query($conn, $reviews_query);

                    // Check if there are reviews for the doctor
                    if (mysqli_num_rows($reviews_result) > 0) {
                        echo "<ul>";
                        // Loop through each review
                        while ($review = mysqli_fetch_assoc($reviews_result)) {
                            echo "<li>Ratings: " . $review['Ratings'] . " - Remarks: " . $review['Remarks'] . "</li>";
                        }
                        echo "</ul>";
                    } else {
                        echo "<p>No reviews found.</p>";
                    }
                    $conn->close();
                ?>
            </div>

        </div>
</body>
</html>
