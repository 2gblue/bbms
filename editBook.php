<?php
session_start();
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SESSION["role"] == '1') {
    // Redirect to a different page or display an error message
    header("location: homepage.php");
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

$user_role = isset($_SESSION["role"]) ? $_SESSION["role"] : null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
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
        <h2 style="text-align:center;"><u>Edit Book</u></h2>
        <br>
        <div class="container container-sub">
            <div class="row" style="margin-top:20px;">
                <div class="col-md-12">
                    <form action="./controllers/editBookController.php?view=<?php echo $view_id; ?>" method="post">
                        <div class="row mb-3">
                            <div class="col">
                                <label for="bookTitle" class="form-label"><b>Book Title</b></label>
                                <input type="text" class="form-control" id="bookTitle" name="bookTitle" value="<?php echo htmlspecialchars($bookTitle); ?>">
                            </div>
                            <div class="col">
                                <label for="pagesNumber" class="form-label"><b>Pages Number</b></label>
                                <input type="number" class="form-control" id="pagesNumber" name="pagesNumber" value="<?php echo htmlspecialchars($pagesNumber); ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="authorName" class="form-label"><b>Author Name</b></label>
                                <input type="text" class="form-control" id="authorName" name="authorName" value="<?php echo htmlspecialchars($authorName); ?>">
                            </div>
                            <div class="col">
                                <label for="quantity" class="form-label"><b>Quantity</b></label>
                                <input type="number" class="form-control" id="quantity" name="quantity" value="<?php echo htmlspecialchars($quantity); ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="publicationCompany" class="form-label"><b>Publication Company</b></label>
                                <input type="text" class="form-control" id="publicationCompany" name="publicationCompany" value="<?php echo htmlspecialchars($publicationCompany); ?>">
                            </div>
                            <div class="col">
                                <label for="genre" class="form-label"><b>Genre</b></label>
                                <input type="text" class="form-control" id="genre" name="genre" value="<?php echo htmlspecialchars($genre); ?>">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="isbn" class="form-label"><b>ISBN</b></label>
                                <input type="text" class="form-control" id="isbn" name="isbn" value="<?php echo htmlspecialchars($isbn); ?>">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary" style="margin-bottom:20px;">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>