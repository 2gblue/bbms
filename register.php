<?php
session_start();
include_once "./controllers/db_connection.php";
$registration_successful = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password']; 
    $user_fullName = $_POST['fullname'];
    $user_phone = $_POST['phone_number'];
    $role = 1; // Default role as user

    $sql = "INSERT INTO user (username, password, role, user_fullName, user_phone) VALUES ('$username', '$password', '$role', '$user_fullName', '$user_phone')";

    if ($conn->query($sql) === TRUE) {
	$registration_successful = true;
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="./custom_css/styles.css">
</head>
<body>
     <?php if ($registration_successful): ?>
        <script type="text/javascript">
            alert("Registration successfull!");
            window.location.href = 'login.php';
        </script>
    <?php endif; ?>
    <div class="container">
        <h2>Register</h2>
        <form method="post" action="register.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required>
            
            <label for="phone_number">Phone Number:</label>
            <input type="text" id="phone_number" name="phone_number" required>
            
            <input type="submit" value="Register">
        </form>
    </div>
</body>
</html>