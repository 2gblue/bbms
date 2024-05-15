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

// Retrieve book data from the database with pagination and search criteria
$sql = "SELECT h.*, hs.status_name, u.id FROM ((history h INNER JOIN history_status hs ON h.status_ID = hs.status_ID) INNER JOIN user u ON h.id = u.id)";
if (!empty($search)) {
    $sql .= " WHERE bookTitle LIKE '%$search%'";
}
if (isset($_GET['genre']) && !empty($_GET['genre']) && $_GET['genre'] !== 'All') {
    $selected_genre = $_GET['genre'];
    $sql .= " WHERE genre = '$selected_genre'";
}
$sql .= " LIMIT $start_from, $records_per_page";

$result = mysqli_query($conn, $sql);

// Count total number of records for pagination
$total_pages_sql = "SELECT COUNT(*) AS total FROM book";
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
                    <a class="navbar-brand navbar-link" href="./bookCatalogue.php">Browse Books</a>
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
        <h2 style="text-align:center;"><u>Book Catalogue</u></h2>
        <br>
        <div class="container container-sub">
            <div class="row">
                <div class="col-md-4">
                    <!-- Search bar -->
                    <form class="input-group mb-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
                        <input type="text" class="form-control" placeholder="Search..." aria-label="Search" aria-describedby="basic-addon2" name="search">
                        <button class="btn btn-outline-secondary" type="submit" id="button-addon2">
                            <i class='bx bx-search-alt-2'></i></button>
                    </form>
                </div>
                
                <div class="col-md-4 text-end" style="margin-left: 350px;">
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

            <div>
            <?php echo $_SESSION["id"] ?>
                <!-- List of Reservations History -->
                <form method="post">
                    <table border="1" class="table" style="width: 100%">
                        <tr class="thread">
                            <th class="th" scope="col">No</th>
                            <th class="th" scope="col">Rental ID</th>
                            <th class="th" scope="col">Book Name</th>
                            <th class="th" scope="col">Date</th>
                            <th class="th" scope="col">Deadline</th>
                            <th class="th" scope="col">Status</th>
                            <th class="th" scope="col">Action</th>
                        </tr>
                        <tr>
                            <?php  if (mysqli_num_rows($result) > 0){
                            // output data of each row
                                $no = 0;
                                while($row = mysqli_fetch_assoc($result) ){
                                $no = $no + 1;
                                $rentID = $row["rental_ID"];
                                $bookTitle = $row["user_fullName"]; //book connect to book books
                                $date = $row["complaint_Date"]; //borrow date
                                $deadline = $row["rental_deadline"];
	                            $status = $row["status_name"];
                            ?>	
	
                                <td class="td"><?php echo $no; ?></td>
                                <td class="td"><?php echo $rentID; ?></td>
		                        <td class="td"><?php echo $bookTitle; ?></td>
                                <td class="td"><?php echo $date; ?></td>
                                <td class="td"><?php echo $deadline; ?></td>
                                <td class="td"><?php echo $status; ?></td>
		                        <td class="td">
                                    <input type="hidden" name="comid" value="<?php echo $complainid; ?>">
                                    <input type="hidden" name="id" value="<?php echo $userid; ?>">
                                <?php

                                if ($status=="Resolved"){
                                ?>
                                <a><button class="button-48" type="button" onclick="window.location.href='/FKEduSearch/Complaint/User/view_reply.php?comid=<?php echo $complainid; ?>';">📧</button></a> 
                                <a><button class="button-48" type="button" onclick="window.location.href='/FKEduSearch/Complaint/User/view.php?comid=<?php echo $complainid; ?>';">👀</button></a> 
			                    <a><button class="button-48" type="button" onclick="window.location.href='/FKEduSearch/Complaint/User/delete.php?comid=<?php echo $complainid; ?>';">🗑️</button></a>
                                <?php
                                }
                                else {
                                ?>
                                <a><button class="button-48" type="button" onclick="window.location.href='/FKEduSearch/Complaint/User/update.php?comid=<?php echo $complainid; ?>';">✏️</button></a> 
                                <a><button class="button-48" type="button" onclick="window.location.href='/FKEduSearch/Complaint/User/view.php?comid=<?php echo $complainid; ?>';">👀</button></a> 
			                    <a><button class="button-48" type="button" onclick="window.location.href='/FKEduSearch/Complaint/User/delete.php?comid=<?php echo $complainid; ?>';">🗑️</button></a>
                                <?php 
                                }
                                ?>
		                        </td>
	                    </tr>
                        <?php
                                }
                            }
                            else{
                                echo "0 results";
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