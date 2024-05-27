<?php
session_start();

include_once "./controllers/db_connection.php";

$id = $_GET['id'];
$sql = "DELETE FROM user WHERE id='$id'";

if ($conn->query($sql) === TRUE) {
    header("Location: viewuser.php");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}
?>
