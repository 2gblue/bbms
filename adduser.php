<?php
session_start();

include_once "./controllers/db_connection.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $user_fullName = $_POST['user_fullName'];
    $user_phone = $_POST['user_phone'];

    $sql = "INSERT INTO user (username, password, role, user_fullName, user_phone) VALUES ('$username', '$password', '$role', '$user_fullName', '$user_phone')";

    if ($conn->query($sql) === TRUE) {
        header("Location: viewuser.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add User</title>
    <link rel="stylesheet" type="text/css" href="./custom_css/style2.css">
</head>
<body>
<div class="container">
    <h1>Add User</h1>
    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <label>Role:</label>
        <input type="number" name="role" required>
        <label>Full Name:</label>
        <input type="text" name="user_fullName">
        <label>Phone:</label>
        <input type="text" name="user_phone">
        <input type="submit" value="Add User">
    </form>
</div>
</body>
</html>
