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

//limit pages for the tables
$records_per_page = 9;
if (isset($_GET['page']) && is_numeric($_GET['page'])) {
    $current_page = $_GET['page'];
} else {
    $current_page = 1;
}
$start_from = ($current_page - 1) * $records_per_page;

$search = '';
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['search'])) {
    $search = $_GET['search'];
}

//session user ID
$userID = $_SESSION["id"];
$role = $_SESSION["role"];

// Retrieve history data from the database with pagination and search criteria
if ($role == "1") {
    $sql = "SELECT h.*, hs.status_name, u.id, bor.*, bo.bookTitle 
            FROM ((((history h 
            INNER JOIN history_status hs ON h.status_ID = hs.status_ID) 
            INNER JOIN user u ON h.id = u.id) 
            INNER JOIN borrow bor ON h.borrowID  = bor.borrowID) 
            INNER JOIN book bo ON bor.bookID = bo.id) 
            WHERE h.id = '$userID' AND h.archived = 0 AND hs.status_ID <> 2";
} else if ($role == "2") {
    $sql = "SELECT h.*, hs.status_name, u.id, bor.*, bo.bookTitle 
    FROM ((((history h 
    INNER JOIN history_status hs ON h.status_ID = hs.status_ID) 
    INNER JOIN user u ON h.id = u.id) 
    INNER JOIN borrow bor ON h.borrowID  = bor.borrowID) 
    INNER JOIN book bo ON bor.bookID = bo.id) 
    WHERE h.archived = 0 AND hs.status_ID <> 2";
}

$sql .= " LIMIT $start_from, $records_per_page";

$result = mysqli_query($conn, $sql);

// Count total number of records for pagination
$total_pages_sql = "SELECT COUNT(*) AS total FROM history";
if (!empty($search)) {
    $total_pages_sql .= " WHERE bookTitle LIKE '%$search%'";
}
$result_total = mysqli_query($conn, $total_pages_sql);
$row_total = mysqli_fetch_assoc($result_total);
$total_records = $row_total['total'];
$total_pages = ceil($total_records / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="./custom_css/navbar.css">
    <link rel="stylesheet" href="./custom_css/layout.css">
    <link rel="stylesheet" href="./custom_css/reservationHistory.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<style>
    .dropbtn {
        background-color: #1F2529;
        color: white;
        padding: 16px;
        font-size: 14px;
        text-decoration: none;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #D8DCFF;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #7749F8;
        color: white;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown:hover .dropbtn {
        background-color: #565676;
    }
</style>

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
                    <a class="navbar-brand navbar-link" href="#" id="bookCatalogueLink" data-role="<?php echo isset($_SESSION["role"]) ? $_SESSION["role"] : ""; ?>">Browse Books</a>
                </div>
                <div class="col">
                    <a class="navbar-brand navbar-link" href="./rentalBook.php">Rentals</a>
                </div>
                <div class="col" style="margin-top:14px;">
                    <div class="dropdown">
                        <a class="dropbtn">History</a>
                        <div class="dropdown-content">
                            <a href="./reservationHistory.php">Reservation History</a>
                            <a href="./reservationReturned.php">Returned</a>
                        </div>
                    </div>
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

    <div class="container container-main">
        <h2 style="text-align:center;"><u>Book Rental</u></h2>
        <br>
        <div class="container container-sub">
            <div class="row" style="margin-bottom:20px">
                <div class="col-md-4">
                    <!-- Search bar -->
                </div>

                <div style="z-index: 10; position:absolute; right:-77%;">
                    <!-- Page directory -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item <?php echo ($current_page == 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($current_page == 1) ? '#' : 'rentalBook.php?page=' . ($current_page - 1); ?>">Prev</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                    <a class="page-link" style="background-color: #7749F8;" href="rentalBook.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo ($current_page == $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($current_page == $total_pages) ? '#' : 'rentalBook.php?page=' . ($current_page + 1); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="row">
                </div>
            </div>
            <br><br>
            <!-- List of Reservations History -->
            <form method="post">
                <table class="table table-hover" style="width: 100%">
                    <tr class="thread">
                        <th class="table-secondary" scope="col">Rental ID</th>
                        <th class="table-secondary" scope="col">Book Name</th>
                        <th class="table-secondary" scope="col">Date</th>
                        <th class="table-secondary" scope="col">Deadline</th>
                        <th class="table-secondary" scope="col">Status</th>
                        <th class="table-secondary" scope="col">Action</th>
                    </tr>
                    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['rental_ID']); ?></td>
                            <td><?php echo htmlspecialchars($row['bookTitle']); ?></td>
                            <td><?php echo htmlspecialchars($row['date']); ?></td>
                            <td><?php echo htmlspecialchars($row['rental_deadline']); ?></td>
                            <td><?php echo htmlspecialchars($row['rental_remark']); ?></td>
                            <td>
                                <?php
                                $borrowDate = new DateTime($row['date']);
                                $currentDate = new DateTime();
                                $interval = $borrowDate->diff($currentDate);
                                if ($interval->days <= 1) {
                                ?>
                                    <a href="editRental.php?rentalID=<?php echo $row['rental_ID']; ?>" class="btn btn-primary">Edit</a>
                                    <a href="cancelController.php?rentalID=<?php echo $row['rental_ID']; ?>" class="btn btn-danger">Cancel</a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            </form>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="./resources/js/navbar.js" defer></script>
</body>

</html>

<script>
    function Confirm() {
        let text = "Are you sure you want to cancel this rental?";
        if (confirm(text) == true) {
            location.href = 'cancelController.php?rentalID=<?php echo $rentalID ?>';
        } else {
            text = "You canceled!";
        }
        document.getElementById("demo").innerHTML = text;
    }
</script>