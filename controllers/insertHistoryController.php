<?php
session_start();
include_once "db_connection.php";

if ($_SERVER["REQUEST_METHOD"]) {
    // Retrieve form data
    $rentID = $_GET['rent'];

    //link to query
    $sql = "UPDATE history SET archived = 1 WHERE rental_ID = '$rentID'";
    $result = mysqli_query($conn,$sql) or die ("Could not add a query");

	$previous_url = $_SERVER['HTTP_REFERER'];
    header('Location: ' . $previous_url);
    exit;
}
?>