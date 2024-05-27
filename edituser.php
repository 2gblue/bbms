<?php
session_start();

include_once "./controllers/db_connection.php";

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $role = $_POST['role'];
    $user_fullName = $_POST['user_fullName'];
    $user_phone = $_POST['user_phone'];

    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $sql = "UPDATE user SET username='$username', password='$password', role='$role', user_fullName='$user_fullName', user_phone='$user_phone' WHERE id='$id'";
    } else {
        $sql = "UPDATE user SET username='$username', role='$role', user_fullName='$user_fullName', user_phone='$user_phone' WHERE id='$id'";
    }

    if ($conn->query($sql) === TRUE) {
        header("Location: viewuser.php");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    $sql = "SELECT * FROM user WHERE id='$id'";
    $result = $conn->query($sql);
    $user = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link rel="stylesheet" type="text/css" href="./custom_css/style2.css">
    
</head>
<body>
<div class="container">
    <h1>Edit User</h1>
    <form method="POST" action="">
        <label>Username:</label>
        <input type="text" name="username" value="<?php echo $user['username']; ?>" required>
        <label>Password (leave blank to keep current password):</label>
        <input type="password" name="password">
        <label>Role:</label>
        <input type="number" name="role" value="<?php echo $user['role']; ?>" required>
        <label>Full Name:</label>
        <input type="text" name="user_fullName" value="<?php echo $user['user_fullName']; ?>">
        <label>Phone:</label>
        <input type="text" name="user_phone" value="<?php echo $user['user_phone']; ?>">
        <input type="submit" value="Update User">
    </form>
</div>
</body>
</html>
