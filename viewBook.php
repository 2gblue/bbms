<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

include_once "./controllers/db_connection.php";

$bookTitle = $pagesNumber = $authorName = $quantity = $publicationCompany = $genre = $isbn = $bookCover = '';

if (isset($_GET['view'])) {
    $view_id = $_GET['view'];
    $sql = "SELECT * FROM book WHERE id = $view_id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $bookTitle = $row['bookTitle'];
        $pagesNumber = $row['pagesNumber'];
        $authorName = $row['authorName'];
        $quantity = $row['quantity'];
        $publicationCompany = $row['publicationCompany'];
        $genre = $row['genre'];
        $isbn = $row['isbn'];
        $bookCover = str_replace("../", "", $row['bookCover']);
    } else {
        header("location: bookCatalogue.php");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    $_SESSION = array();
    session_destroy();
    header("location: login.php");
    exit;
}

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

$user_role = isset($_SESSION["role"]) ? $_SESSION["role"] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./custom_css/navbar.css">
    <link rel="stylesheet" href="./custom_css/layout.css">
    <link rel="stylesheet" href="./custom_css/bookCatalogue.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        input[readonly] {
            background-color: #f8f9fa;
            opacity: 0.7;
        }
    </style>
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
                    <a class="navbar-brand navbar-link" href="#" id="bookCatalogueLink"
                        data-role="<?php echo isset($_SESSION["role"]) ? $_SESSION["role"] : ""; ?>">Browse Books</a>
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
        <h2 style="text-align:center;"><u>View Book</u></h2>
        <br>
        <div class="container container-sub">
            <div class="row" style="margin-top:20px;">
                <div class="col-md-12">
                    <div class="row mb-3">
                        <div class="col">
                            <br>
                            <?php if (!empty($bookCover)): ?>
                                <img src="<?php echo htmlspecialchars($bookCover); ?>" alt="Book Cover"
                                    style="max-width: 40%; height: auto;">
                            <?php else: ?>
                                <p>No book cover available</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <br>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="bookTitle" class="form-label"><b>Book Title</b></label>
                            <input type="text" class="form-control" id="bookTitle" name="bookTitle"
                                value="<?php echo htmlspecialchars($bookTitle); ?>" readonly>
                        </div>
                        <div class="col">
                            <label for="pagesNumber" class="form-label"><b>Pages Number</b></label>
                            <input type="number" class="form-control" id="pagesNumber" name="pagesNumber"
                                value="<?php echo htmlspecialchars($pagesNumber); ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="authorName" class="form-label"><b>Author Name</b></label>
                            <input type="text" class="form-control" id="authorName" name="authorName"
                                value="<?php echo htmlspecialchars($authorName); ?>" readonly>
                        </div>
                        <div class="col">
                            <label for="quantity" class="form-label"><b>Quantity</b></label>
                            <input type="number" class="form-control" id="quantity" name="quantity"
                                value="<?php echo htmlspecialchars($quantity); ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="publicationCompany" class="form-label"><b>Publication Company</b></label>
                            <input type="text" class="form-control" id="publicationCompany" name="publicationCompany"
                                value="<?php echo htmlspecialchars($publicationCompany); ?>" readonly>
                        </div>
                        <div class="col">
                            <label for="genre" class="form-label"><b>Genre</b></label>
                            <input type="text" class="form-control" id="genre" name="genre"
                                value="<?php echo htmlspecialchars($genre); ?>" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col">
                            <label for="isbn" class="form-label"><b>ISBN</b></label>
                            <input type="text" class="form-control" id="isbn" name="isbn"
                                value="<?php echo htmlspecialchars($isbn); ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($user_role == 1): ?>
                <button type="button" class="btn btn-danger" style="margin-bottom:20px;" data-bs-toggle="modal"
                    data-bs-target="#rentBookModal">Rent Now</button>
            <?php endif; ?>
        </div>

        <!-- Rent Book Modal -->
        <div class="modal fade" id="rentBookModal" tabindex="-1" aria-labelledby="rentBookModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rentBookModalLabel">Rent Book</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="viewBook.php?view=<?php echo $view_id; ?>" method="post">
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
                            <input type="hidden" name="bookID" value="<?php echo $view_id; ?>">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" name="borrow_book">Borrow</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>