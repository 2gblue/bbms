<?php
session_start();

include_once "./controllers/db_connection.php";

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
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

    // Calculate rental deadline
    $rentalDeadline = date('Y-m-d', strtotime($newDate . ' + 7 days'));

    $sql = "UPDATE borrow SET date = ?, time = NOW() WHERE borrowID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newDate, $rental['borrowID']);
    if ($stmt->execute()) {
        $sql = "UPDATE history SET rental_remark = ?, rental_deadline = ? WHERE rental_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $newDescription, $rentalDeadline, $rentalID);
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
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./custom_css/navbar.css">
    <link rel="stylesheet" href="./custom_css/layout.css">
    <link rel="stylesheet" href="./custom_css/bookCatalogue.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <div class="row">
                <div class="col">
                    <p class="navbar-brand">Book Borrowing Management System</p>
                </div>
                <div class="col">
                    <a class="navbar-brand navbar-link" href="./homepage.php">Home</a>
                </div>
                <div class="col">
                    <a class="navbar-brand navbar-link" href="./bookCatalogue.php">Browse Books</a>
                </div>
                <div class="col">
                    <a class="navbar-brand navbar-link" href="./rentalBook.php">Rentals</a>
                </div>
                <div class="col">
                    <a class="navbar-brand navbar-link" href="./reservationHistory.php">History</a>
                </div>
                <div class="col">
                    <a class="navbar-brand navbar-link" href="./reporting.php">Analytics</a>
                </div>
            </div>
            <form class="d-flex" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="dropdown">
                    <button class="btn btn-outline-light" type="button" id="dropdownMenuButton"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo htmlspecialchars($_SESSION["username"]); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><button class="dropdown-item" type="submit" name="logout">Logout</button></li>
                    </ul>
                </div>
            </form>
        </div>
    </nav>

    <div class="container container-main">
        <h2 style="text-align:center;"><u>Edit Borrow Details</u></h2>
        <br>
        <div class="container container-sub">
            <div class="row">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?rentalID=' . $rentalID; ?>"
                    method="post">
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
                    <button style="margin-bottom: 10px;" type="submit" class="btn btn-primary" name="update">Update Rental</button>
                    <button style="margin-bottom: 10px;" type="button" class="btn btn-primary" onclick="window.location.href='rentalBook.php'">Cancel</button>
                </form>
            </div>
        </div>
</body>

</html>