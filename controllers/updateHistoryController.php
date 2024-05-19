<?php
session_start();
include_once "db_connection.php";

if ($_SERVER["REQUEST_METHOD"]) {
    // Retrieve form data
    $rentID = $_GET['rent'];
    $status = $_POST["statusid"];
    $remark = $_POST["remark"];

    //link to query
    $sql = "UPDATE history SET status_ID = '$status', rental_remark = '$remark' WHERE rental_ID = '$rentID'";

    $result = mysqli_query($conn,$sql) or die ("Could not execute query in update.php");
	
    echo "<script type = 'text/javascript'> window.location='/bbms/reservationEdit.php?historyid=$rentID' </script>";
}
?>