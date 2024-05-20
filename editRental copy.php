<?php
session_start();

include_once "./controllers/db_connection.php";

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Function to check if the rental can be edited or canceled
function canEditOrCancel($borrowDate)
{
    $borrowDateTime = new DateTime($borrowDate);
    $currentDateTime = new DateTime();
    $interval = $currentDateTime->diff($borrowDateTime);
    return $interval->days < 1;
}

// Retrieve rental details
if (isset($_GET['rentalID']) && is_numeric($_GET['rentalID'])) {
    $rentalID = $_GET['rentalID'];

    $sql = "SELECT h.*, bor.*, bo.bookTitle 
            FROM history h 
            INNER JOIN borrow bor ON h.borrowID = bor.borrowID 
            INNER JOIN book bo ON bor.bookID = bo.id 
            WHERE h.rental_ID = ? AND h.archived = 0";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $rentalID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $rental = $result->fetch_assoc();
    } else {
        echo "Invalid rental ID.";
        exit;
    }
    $stmt->close();
} else {
    echo "Invalid rental ID.";
    exit;
}

// Update rental details
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $newDate = $_POST['date'];
    $newDescription = $_POST['description'];

    if (canEditOrCancel($rental['date'])) {
        $sql = "UPDATE borrow SET date = ?, time = NOW() WHERE borrowID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $newDate, $rental['borrowID']);
        if ($stmt->execute()) {
            $sql = "UPDATE history SET rental_remark = ? WHERE rental_ID = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $newDescription, $rentalID);
            if ($stmt->execute()) {
                echo "Rental updated successfully.";
                header("location: rentalBook.php");
                exit;
            } else {
                echo "Error updating rental: " . $conn->error;
            }
        } else {
            echo "Error updating borrow date: " . $conn->error;
        }
        $stmt->close();
    } else {
        echo "Cannot edit the rental after one day from the borrowing date.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <title>Edit Rental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <h2>Edit Rental</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?rentalID=' . $rentalID; ?>" method="post">
            <div class="mb-3">
                <label for="date" class="form-label">Borrow Date</label>
                <input type="date" class="form-control" id="date" name="date"
                    value="<?php echo htmlspecialchars($rental['date']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"
                    required><?php echo htmlspecialchars($rental['rental_remark']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="update">Update Rental</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>