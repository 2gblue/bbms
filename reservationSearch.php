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
$search = $_POST["search"];

// Retrieve history data from the database with pagination and search criteria
if ($role == "1") {
    $sql = "SELECT h.*, hs.status_name, u.id 
            FROM ((history h 
            INNER JOIN history_status hs ON h.status_ID = hs.status_ID) 
            INNER JOIN user u ON h.id = u.id) 
            INNER JOIN borrow bor ON h.borrowID  = bor.borrowID) 
            INNER JOIN book bo ON bor.bookID = bo.id) 
            WHERE h.id = '$userID' AND h.archived = 0 AND h.rental_ID = '$search'";
}
else if($role == "2"){
    $sql = "SELECT h.*, hs.status_name, u.id 
            FROM ((history h 
            INNER JOIN history_status hs ON h.status_ID = hs.status_ID) 
            INNER JOIN user u ON h.id = u.id) 
            INNER JOIN borrow bor ON h.borrowID  = bor.borrowID) 
            INNER JOIN book bo ON bor.bookID = bo.id) 
            WHERE h.archived = 0 AND h.rental_ID = '$search'";
}

$sql .= " LIMIT $start_from, $records_per_page";

$result = mysqli_query($conn, $sql);

// Count total number of records for pagination
$total_pages_sql = "SELECT COUNT(*) AS total FROM history WHERE rental_ID = '$search'";

$result_total = mysqli_query($conn, $total_pages_sql);
$row_total = mysqli_fetch_assoc($result_total);

$total_records = $row_total['total'];
$total_pages = ceil($total_records / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">

<!-- Header -->
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


    <div class="container container-main">
        <h2 style="text-align:center;"><u>Book Rental History</u></h2>
        <br>
        <div class="container container-sub">
            <div class="row">
                <div class="col-md-4">

                    <!-- Search bar -->
                    <form class="input-group mb-3" action="reservationSearch.php" method="post">
                        <input type="text" class="form-control" placeholder="Search..." aria-label="Search" aria-describedby="basic-addon2" name="search">
                        <button class="btn btn-outline-secondary" type="submit" id="button-addon2">
                            <i class='bx bx-search-alt-2'></i></button>
                    </form>
                </div>
                
                <div style="z-index: 10; position:absolute; right:-77%;">
                    <!-- Page directory -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item <?php echo ($current_page == 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($current_page == 1) ? '#' : 'reservationHistory.php?page=' . ($current_page - 1); ?>">Prev</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="reservationHistory.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo ($current_page == $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($current_page == $total_pages) ? '#' : 'reservationHistory.php?page=' . ($current_page + 1); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <!-- Archive button -->
            <p>
            <button class="btn btn-success" type="button" id="button-addon2" style="z-index: 10; position:absolute; right: 12.5%;" onclick="window.location.href='/bbms/reservationArchive.php';">Archive</button>
            </p>

            <div>
                <!-- List of Reservations History -->
                <form method="post">
                    <table border="1" class="table table-hover" style="width: 100%">
                        <tr class="thread">
                            <th class="table-secondary" scope="col">Rental ID</th>
                            <th class="table-secondary" scope="col">Book Name</th>
                            <th class="table-secondary" scope="col">Date</th>
                            <th class="table-secondary" scope="col">Deadline</th>
                            <th class="table-secondary" scope="col">Status</th>
                            <th class="table-secondary" scope="col">Action</th>
                        </tr>
                        <tr>
                            <?php  if (mysqli_num_rows($result) > 0){
                            // output data of each row
                                while($row = mysqli_fetch_assoc($result) ){
                                $rentID = $row["rental_ID"];
                                $bookTitle = $row["bookTitle"]; //book connect to book books
                                $date = $row["date"]; //borrow date
                                $deadline = $row["rental_deadline"];
	                            $status = $row["status_name"];
                            ?>	
                                <td><?php echo $rentID; ?></td>
		                        <td><?php echo $bookTitle; ?></td>
                                <td><?php echo $date; ?></td>
                                <td><?php echo $deadline; ?></td>
                                <td><?php echo $status; ?></td>
		                        <td>
                                <a><button class="btn btn-light" type="button" onclick="window.location.href='/bbms/reservationView.php';"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/></svg></button></a> 
                                <?php 
                                if ($role == "2") {
                                ?>
                                <a><button class="btn btn-light" type="button" onclick="window.location.href='/bbms/reservationEdit.php?historyid=<?php echo $rentID; ?>';"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-brush-fill" viewBox="0 0 16 16"><path d="M15.825.12a.5.5 0 0 1 .132.584c-1.53 3.43-4.743 8.17-7.095 10.64a6.1 6.1 0 0 1-2.373 1.534c-.018.227-.06.538-.16.868-.201.659-.667 1.479-1.708 1.74a8.1 8.1 0 0 1-3.078.132 4 4 0 0 1-.562-.135 1.4 1.4 0 0 1-.466-.247.7.7 0 0 1-.204-.288.62.62 0 0 1 .004-.443c.095-.245.316-.38.461-.452.394-.197.625-.453.867-.826.095-.144.184-.297.287-.472l.117-.198c.151-.255.326-.54.546-.848.528-.739 1.201-.925 1.746-.896q.19.012.348.048c.062-.172.142-.38.238-.608.261-.619.658-1.419 1.187-2.069 2.176-2.67 6.18-6.206 9.117-8.104a.5.5 0 0 1 .596.04"/></svg></button></a> 
                                <?php 
                                }
                                ?>
                                
			                    <a><button class="btn btn-light" type="button" onclick="Confirm()"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-archive" viewBox="0 0 16 16"><path d="M0 2a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v2a1 1 0 0 1-1 1v7.5a2.5 2.5 0 0 1-2.5 2.5h-9A2.5 2.5 0 0 1 1 12.5V5a1 1 0 0 1-1-1zm2 3v7.5A1.5 1.5 0 0 0 3.5 14h9a1.5 1.5 0 0 0 1.5-1.5V5zm13-3H1v2h14zM5 7.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5"/></svg></button></a>
		                        </td>
	                    </tr>
                        <?php
                                }
                            }
                            else if($search == null){
                                $message = "Please type in the search box!";
                                echo "<script type='text/javascript'>alert('$message');</script>";
                                echo "<script type = 'text/javascript'> window.location='reservationHistory.php' </script>";
                            }
                            else{
                                $message = "Sorry. Search cannot be found...";
                                echo "<script type='text/javascript'>alert('$message');</script>";
                                echo "<script type = 'text/javascript'> window.location='reservationHistory.php' </script>";
                            }
                        ?>
                    </table>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>

<script>
function Confirm() {
  let text = "Are you sure you want to ARCHIVE this history?";
  if (confirm(text) == true) {
    location.href = '/bbms/controllers/insertHistoryController.php?rent=<?php echo $rentID?>';
  } else {
    text = "You canceled!";
  }
  document.getElementById("demo").innerHTML = text;
}
</script>