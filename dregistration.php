<?php
include 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $Username = $_POST['Username'];
    $specialisation = $_POST['specialisation'];
    $fees = $_POST['Fees'];

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO doctor (Email, Password, Username, Specialisation, Fees)
            VALUES ('$email', '$hashed_password', '$Username', '$specialisation', '$fees')";

    if ($conn->query($sql) === TRUE) {
        header("Location: dlogin.php");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Registration</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Doctor Registration</h1>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required>
            </div><br>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>
            </div><br>

            <div class="form-group">
                <label for="name">Username:</label>
                <input type="text" name="Username" id="Username" required>
            </div>
            <div class="form-group">
                <label for="specialisation">Specialisation:</label>
                <input type="text" name="specialisation" id="specialisation" required>
            </div>
            <div class="form-group">
                <label for="fee">Fee:</label>
                <input type="number" name="Fees" id="fees" required>
            </div>
            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>
