<?php
session_start();

include_once "./controllers/db_connection.php";

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check user's role
if ($_SESSION["role"] == '1') {
    // Redirect to a different page or display an error message
    header("location: homepage.php");
    exit;
}

// Logout logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    $_SESSION = array();
    session_destroy();
    header("location: login.php");
    exit;
}

$records_per_page = 10; // Number of records to display per page
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1; // Get current page number from URL parameter

$offset = ($current_page - 1) * $records_per_page;

$sql = "SELECT id, bookTitle, quantity FROM book";

if (isset($_GET['genre'])) {
    $genre = $_GET['genre'];
    $sql .= " WHERE genre = '$genre'";
}

if (isset($_GET['search'])) {
    $search = $_GET['search'];
    $sql .= " WHERE bookTitle LIKE '%$search%'";
}

$sql .= " LIMIT $offset, $records_per_page";
$result = mysqli_query($conn, $sql);

$total_records_sql = "SELECT COUNT(*) AS total FROM book";
$total_records_result = mysqli_query($conn, $total_records_sql);
$total_records = mysqli_fetch_assoc($total_records_result)['total'];
$total_pages = ceil($total_records / $records_per_page);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Books</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
                <div class="col-md-4">
                    <!-- Dropdown for genre -->
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            Genre
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="./bookCatalogueManage.php">All</a></li>
                            <li><a class="dropdown-item" href="./bookCatalogueManage.php?genre=Educational">Educational</a></li>
                            <li><a class="dropdown-item" href="./bookCatalogueManage.php?genre=Fiction">Fiction</a></li>
                            <li><a class="dropdown-item" href="./bookCatalogueManage.php?genre=Non-Fiction">Non-Fiction</a></li>
                            <li><a class="dropdown-item" href="./bookCatalogueManage.php?genre=Biographies">Biographies</a></li>
                            <li><a class="dropdown-item" href="./bookCatalogueManage.php?genre=Cooking">Cooking</a></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <!-- Page directory -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination">
                            <li class="page-item <?php echo ($current_page == 1) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($current_page == 1) ? '#' : 'bookCatalogueManage.php?page=' . ($current_page - 1); ?>">Prev</a>
                            </li>
                            <?php for ($i = 1; $i <= $total_pages; $i++) : ?>
                                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="bookCatalogueManage.php?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            <li class="page-item <?php echo ($current_page == $total_pages) ? 'disabled' : ''; ?>">
                                <a class="page-link" href="<?php echo ($current_page == $total_pages) ? '#' : 'bookCatalogueManage.php?page=' . ($current_page + 1); ?>">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <div class="col-md-4">
                    <a href="./addBook.php"><button type="button" class="btn btn-success">+ New Entry</button></a>
                </div>
                <div class="container" style="margin-top:20px;">
                    <table class="table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Book Name</th>
                                <th scope="col">Quantity</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Output data of each row
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr style='width:10%;'>";
                                echo "<th scope='row'>" . $row["id"] . "</th>";
                                echo "<td style='width:60%;'>" . $row["bookTitle"] . "</td>";
                                echo "<td style='width:10%;'>" . $row["quantity"] . "</td>";
                                echo "<td style='width:20%;'>";
                                // Link to viewBook.php with the corresponding id
                                echo "<a href='viewBook.php?view=" . $row["id"] . "' class='btn btn-outline-secondary'><i class='bx bx-detail'></i></a>";
                                echo "<a href='editBook.php?view=" . $row["id"] . "' class='btn btn-outline-secondary'><i class='bx bx-edit'></i></a>";
                                echo "<button class='btn btn-outline-secondary' data-bs-toggle='modal' data-bs-target='#deleteModal' data-id='" . $row["id"] . "'><i class='bx bxs-trash'></i></button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="deleteForm" action="./controllers/deleteBookController.php" method="post">
                        <div class="modal-header">
                            <h5 class="modal-title" id="deleteModalLabel">Delete Confirmation</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete the selection?
                            <input type="hidden" name="delete_book" value="true">
                            <input type="hidden" id="bookId" name="book_id" value="">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            // Set the book ID in the hidden input field when the delete button is clicked
            var deleteButtons = document.querySelectorAll('[data-bs-target="#deleteModal"]');
            deleteButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var bookId = this.getAttribute('data-id');
                    document.getElementById('bookId').value = bookId;
                });
            });
        </script>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>