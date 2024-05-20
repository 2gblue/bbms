<?php
session_start();

include_once "./controllers/db_connection.php";

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check if rentalID is provided
if (isset($_GET['rentalID']) && is_numeric($_GET['rentalID'])) {
    $rentalID = $_GET['rentalID'];

    // Get borrowID from history
    $sql = "SELECT borrowID FROM history WHERE rental_ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rentalID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $borrowID = $row['borrowID'];

        // Delete from history table first
        $sql = "DELETE FROM history WHERE borrowID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $borrowID);
        if ($stmt->execute()) {
            // Then delete from borrow table
            $sql = "DELETE FROM borrow WHERE borrowID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $borrowID);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Rental canceled successfully.";
            } else {
                $_SESSION['message'] = "Error deleting borrow record: " . $conn->error;
            }
        } else {
            $_SESSION['message'] = "Error deleting rental history: " . $conn->error;
        }
    } else {
        $_SESSION['message'] = "Invalid rental ID.";
    }
    $stmt->close();
} else {
    $_SESSION['message'] = "Invalid rental ID.";
}

$conn->close();
header("location: rentalBook.php");
exit;
?>