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

// Borrow book logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['borrow_book'])) {
    $bookID = $_POST['bookID'];
    $userID = $_SESSION['id'];  // Assuming user ID is stored in session
    $date = $_POST['date'];
    $description = $_POST['description'];
    $time = date('G') . date('i');
    // $time = date('H:i:s');  // Current time
    $rental_deadline = date('Y-m-d', strtotime($date . ' + 7 days'));  // 7 days after the rental date

    $borrow_sql = "INSERT INTO borrow (bookID, userID, time, date) VALUES ('$bookID', '$userID', '$time', '$date')";
    if (mysqli_query($conn, $borrow_sql)) {
        $borrowID = mysqli_insert_id($conn);
        $history_sql = "INSERT INTO history (id, borrowID, status_ID, rental_deadline, rental_remark, archived) VALUES ('$userID', '$borrowID', 1, '$rental_deadline', '$description', 0)";
        mysqli_query($conn, $history_sql);
        echo "<script>alert('Book borrowed successfully');</script>";
    } else {
        echo "<script>alert('Error borrowing book');</script>";
    }
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
$sql = "SELECT id, bookTitle, genre, quantity, bookCover FROM book";
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
                    <a class="navbar-brand navbar-link" href="#">Analytics</a>
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
        <h2 style="text-align:center;"><u>Book Catalogue</u></h2>
        <br>
        <div class="container container-sub">
            <div class="row">
                <div class="col-md-4">
                    <!-- Search bar -->
                    <form class="input-group mb-3" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"
                        method="get">
                        <input type="text" class="form-control" placeholder="Search..." aria-label="Search"
                            aria-describedby="basic-addon2" name="search">
                        <button class="btn btn-outline-secondary" type="submit" id="button-addon2">
                            <i class='bx bx-search-alt-2'></i></button>
                    </form>
                </div>
                <div class="col-md-4">
                    <!-- Dropdown for genre -->
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Genre
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="bookCatalogue.php">All</a></li>
                            <li><a class="dropdown-item" href="bookCatalogue.php?genre=Educational">Educational</a></li>
                            <li><a class="dropdown-item" href="bookCatalogue.php?genre=Fiction">Fiction</a></li>
                            <li><a class="dropdown-item" href="bookCatalogue.php?genre=Non-Fiction">Non-Fiction</a></li>
                            <li><a class="dropdown-item" href="bookCatalogue.php?genre=Biographies">Biographies</a></li>
                            <li><a class="dropdown-item" href="bookCatalogue.php?genre=Cooking">Cooking</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <!-- Page directory -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item <?php echo ($current_page == 1) ? 'disabled' : ''; ?>">
                                <a class="page-link"
                                    href="<?php echo ($current_page == 1) ? '#' : 'bookCatalogue.php?page=' . ($current_page - 1); ?>">Prev</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                    <a class="page-link"
                                        href="bookCatalogue.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo ($current_page == $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link"
                                    href="<?php echo ($current_page == $total_pages) ? '#' : 'bookCatalogue.php?page=' . ($current_page + 1); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

            <div class="row">
                <?php
                // Loop through each book and create a card for it
                while ($row = mysqli_fetch_assoc($result)) {
                    // Concatenate the correct directory path with the bookCover data
                    $bookCoverPath = "./uploads/books/" . basename($row['bookCover']);
                    ?>
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div style="height: 0; padding-bottom: 56.25%; overflow: hidden;">
                                <img class="card-img-top" src="<?php echo $bookCoverPath; ?>" alt="Card image cap">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $row['bookTitle']; ?></h5>
                                <p class="card-text"><b>Genre:</b> <?php echo $row['genre']; ?><br><b>Quantity:</b>
                                    <?php echo $row['quantity']; ?></p>
                                <a href="viewBook.php?view=<?php echo $row['id']; ?>" class="btn btn-info">View</a>
                                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rentBookModal"
                                    data-bookid="<?php echo $row['id']; ?>">Rent Now</button>
                            </div>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Rent Book Modal -->
    <div class="modal fade" id="rentBookModal" tabindex="-1" aria-labelledby="rentBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rentBookModalLabel">Rent Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="bookCatalogue.php" method="post">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                required></textarea>
                        </div>
                        <input type="hidden" name="bookID" id="bookID">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" name="borrow_book">Borrow</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script>
        var rentBookModal = document.getElementById('rentBookModal');
        rentBookModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var bookID = button.getAttribute('data-bookid');
            var modalBodyInput = rentBookModal.querySelector('#bookID');
            modalBodyInput.value = bookID;
        });
    </script>
</body>

</html>