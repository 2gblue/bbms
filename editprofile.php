<?php
session_start();

include_once "./controllers/db_connection.php";

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user = null; // Define $user variable outside the else block

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_SESSION['username'];
    $user_fullName = $_POST['user_fullName'];
    $user_phone = $_POST['user_phone'];

    $sql = "UPDATE user SET user_fullName='$user_fullName', user_phone='$user_phone' WHERE username='$username'";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Profile updated successfully.'); window.location.href = 'profile.php';</script>";
       
    } else {
        echo "<script>alert('Error updating profile: " . $conn->error . "');</script>";
    }
} else {
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM user WHERE username='$username'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Profile</title>
    <link rel="stylesheet" type="text/css" href="./custom_css/style2.css">
</head>
<body>
    <div class="container">
        <h2>Edit Profile</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="fullname">Full Name:</label>
                <input type="text" id="user_fullName" name="user_fullName" value="<?php echo isset($user['user_fullName']) ? htmlspecialchars($user['user_fullName']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone_number">Phone Number:</label>
                <input type="text" id="user_phone" name="user_phone" value="<?php echo isset($user['user_phone']) ? htmlspecialchars($user['user_phone']) : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <input type="submit" value="Update">
            </div>
        </form>
        <div class="form-group">
            <a href="profile.php" class="button">Back to Profile</a>
        </div>
    </div>
</body>
</html>
