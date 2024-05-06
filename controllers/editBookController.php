<?php
session_start();
include_once "db_connection.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $bookTitle = $_POST['bookTitle'];
    $pagesNumber = $_POST['pagesNumber'];
    $authorName = $_POST['authorName'];
    $quantity = $_POST['quantity'];
    $publicationCompany = $_POST['publicationCompany'];
    $genre = $_POST['genre'];
    $isbn = $_POST['isbn'];

    // Update database
    $view_id = $_GET['view'];
    $sql = "UPDATE book SET 
            bookTitle = ?, 
            pagesNumber = ?, 
            authorName = ?, 
            quantity = ?, 
            publicationCompany = ?, 
            genre = ?, 
            isbn = ?
            WHERE id = ?";

    if ($stmt = mysqli_prepare($conn, $sql)) {
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param(
            $stmt,
            "sisisssi",
            $bookTitle,
            $pagesNumber,
            $authorName,
            $quantity,
            $publicationCompany,
            $genre,
            $isbn,
            $view_id
        );

        // Execute the prepared statement
        if (mysqli_stmt_execute($stmt)) {
            header("location: ../bookCatalogueManage.php");
            exit;
        } else {
            echo "Error updating record: " . mysqli_error($conn);
        }

        // Close statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
}
