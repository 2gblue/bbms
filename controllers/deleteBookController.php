<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if the ID parameter is provided in the request
if (isset($_POST['delete_book']) && isset($_POST['book_id'])) {
    // Include database connection
    include_once "db_connection.php";

    // Prepare and execute SQL query to delete the record
    $id = $_POST['book_id'];
    $sql = "DELETE FROM book WHERE id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        if (mysqli_stmt_execute($stmt)) {
            // Redirect back to the book catalogue page after successful deletion
            header("location: ../bookCatalogueManage.php");
            exit;
        } else {
            echo "Error executing deletion query: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing deletion statement: " . mysqli_error($conn);
    }
} else {
    // Redirect to the book catalogue page if ID parameter is missing
    header("location: ../bookCatalogueManage.php");
    exit;
}
