<?php
session_start();

include_once "./controllers/db_connection.php";

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Logout logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    $_SESSION = array();
    session_destroy();
    header("location: login.php");
    exit;
}

$search = '';
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search = $_GET['search'];
}

//session user ID
$userID = $_SESSION["id"];
$role = $_SESSION["role"];

//get history ID
$rentID = $_GET['historyid'];

// Retrieve history data from the database with pagination and search criteria
$sql = "SELECT book.*, bor.*, h.*, hs.status_name, u.*
        FROM ((((history h 
        INNER JOIN history_status hs ON h.status_ID = hs.status_ID) 
        INNER JOIN borrow bor ON h.borrowID = bor.borrowID) 
        INNER JOIN book book ON bor.bookID  = book.id) 
        INNER JOIN user u ON h.id = u.id) 
        WHERE h.rental_ID = '$rentID'";

$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc( $result );

//book info from the database
$bookCover = str_replace("../", "", $row["bookCover"]);
$bookTitle = $row["bookTitle"]; //book connect to book books
$isbn = $row["date"]; //borrow date

//borrow & rental info from the database
$time = $row["time"];
$date = $row["date"];
$deadline = $row["rental_deadline"];
$status= $row["status_name"];
$remark = $row["remark"];

//the user who borrow, info
$name = $row["user_fullName"];
$matric = $row["user_matric"];
$faculty = $row["user_faculty"];
$phone = $row["user_phone"];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./custom_css/navbar.css">
    <link rel="stylesheet" href="./custom_css/layout.css">
    <link rel="stylesheet" href="./custom_css/reservationHistory.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<!-- Top Nav -->
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
                    <a class="navbar-brand navbar-link" href="./bookCatalogueManage.php">Browse Books</a>
                </div>
                <div class="col">
                    <a class="navbar-brand navbar-link" href="#">Rentals</a>
                </div>
                <div class="col">
                    <a class="navbar-brand navbar-link" href="./reservationHistory.php">History</a>
                </div>
                <div class="col">
                    <a class="navbar-brand navbar-link" href="#">Analytics</a>
                </div>
            </div>
            <form class="d-flex" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="dropdown">
                    <button class="btn btn-outline-light" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo htmlspecialchars($_SESSION["username"]); ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                        <li><button class="dropdown-item" type="submit" name="logout">Logout</button></li>
                    </ul>
                </div>
            </form>
        </div>
    </nav>
<!-- Top Nav -->


<!-- Page Content -->
    <div class="container container-main">

    <!-- Title -->
        <h2 style="text-align:center;"><u>Rental Details</u></h2>
        <br>

        <div class="container container-sub" style="overflow:scroll; height:auto; padding-bottom:50px;">
            <div class="row" style="margin-top:20px;">
                <div class="col-md-12">
                    <div class="row mb-3">
                        <div class="col">
                            <br>
                            <?php if (!empty($bookCover)) : ?>
                                <img src="<?php echo htmlspecialchars($bookCover); ?>" alt="Book Cover" style="max-width: 40%; height: auto;">
                            <?php else : ?>
                                <p>No book cover available</p>
                            <?php endif; ?>
                        </div>
                        <div class="col">
                            <div class="row mb-3">
                                <div class="col" style="margin-right:-300px; margin-left:250px;">
                                    <label for="time" class="form-label"><b>Time</b></label>
                                </div>
                                <div class="col" style="margin-left:150px; margin-top:-10px;">
                                    <input type="text" class="form-control" id="time" name="time" value="<?php echo htmlspecialchars($time); ?>" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col" style="margin-right:-300px; margin-left:250px;">
                                    <label for="date" class="form-label"><b>Date</b></label>
                                </div>
                                <div class="col" style="margin-left:150px; margin-top:-10px;">
                                    <input type="text" class="form-control" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col" style="margin-right:-300px; margin-left:250px;">
                                    <label for="deadline" class="form-label"><b>Deadline</b></label>
                                </div>
                                <div class="col" style="margin-left:150px; margin-top:-10px;">
                                    <input type="text" class="form-control" id="deadline" name="deadline" value="<?php echo htmlspecialchars($deadline); ?>" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="bookTitle" class="form-label"><b>Book Title</b></label>
                        </div>
                        <div class="col" style="margin-left:-750px; margin-top:-10px;">
                            <input type="text" class="form-control" id="bookTitle" name="bookTitle" value="<?php echo $bookTitle; ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="isbn" class="form-label"><b>ISBN</b></label>
                        </div>
                        <div class="col" style="margin-left:-750px; margin-top:-10px;">
                            <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="status" class="form-label"><b>Status</b></label>
                        </div>
                        <div class="col" style="margin-top:-13px; margin-left:-360px;">
                            <div class="input-group-append">
                            <select class="custom-select" id="statusid" style="border-radius: 5px; padding:10px;">
                            <option selected><?php echo htmlspecialchars($status); ?></option>
                            <option value="1">Borrowing</option>
                            <option value="2">Returned</option>
                            <option value="3">Missing</option>
                            <option value="4">Damaged</option>
                            </select>
                            </div>
                        </div>
                        <div class="col" style="margin-right:-400px; margin-left:-300px;">
                            <label for="remark" class="form-label"><b>Remark</b></label>
                        </div>
                        <div class="col" style="margin-top:-10px;">
                            <input type="text" class="form-control" id="remark" name="remark" value="<?php echo htmlspecialchars($remark); ?>">
                        </div>
                    </div>
                    <div class="row mb-3">
                    </div>
                    <div class="row mb-3">
                        <h2 style="text-align:left;">Borrower Info</h2>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="fullName" class="form-label"><b>Name</b></label>
                        </div>
                        <div class="col" style="margin-left:-750px; margin-top:-10px;">
                            <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo htmlspecialchars($name); ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="matricID" class="form-label"><b>Matric ID</b></label>
                        </div>
                        <div class="col" style="margin-left:-750px; margin-top:-10px;">
                            <input type="text" class="form-control" id="matricID" name="matricID" value="<?php echo htmlspecialchars($matric); ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="faculty" class="form-label"><b>Faculty</b></label>
                        </div>
                        <div class="col" style="margin-left:-750px; margin-top:-10px;">
                            <input type="text" class="form-control" id="faculty" name="faculty" value="<?php echo htmlspecialchars($faculty); ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="phone" class="form-label"><b>Phone Number</b></label>
                        </div>
                        <div class="col" style="margin-left:-750px; margin-top:-10px;">
                            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                           <!-- Previous Page button -->
                            <button class="btn btn-success" type="button" id="button-addon2" style="z-index: 10; position:absolute; right: 12.5%;" onclick="history.back();">Back</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="./resources/js/navbar.js" defer></script>

        </div>

<!-- To ensure the bootstrap items is working -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybBogGz1H5A8Y6HN9A7C1K1bGuhrBxl3iWv2lfVH6wR8d4Eu3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+7HAuoJl+0I4u0g8JGuhGflf4x6dz" crossorigin="anonymous"></script>
</body>

</html>