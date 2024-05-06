<?php
session_start();

// Check if user is not logged in, redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Include database connection file
include_once "db_connection.php";

// Define variables and initialize with empty values
$bookTitle = $pagesNumber = $authorName = $quantity = $publicationCompany = $genre = $isbn = "";
$bookCover = ""; // File path of the uploaded book cover image

// Process form submission when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate book title
    if (empty(trim($_POST["bookTitle"]))) {
        $bookTitle_err = "Please enter book title.";
    } else {
        $bookTitle = trim($_POST["bookTitle"]);
    }

    // Validate pages number
    if (empty(trim($_POST["pagesNumber"]))) {
        $pagesNumber_err = "Please enter pages number.";
    } else {
        $pagesNumber = trim($_POST["pagesNumber"]);
    }

    // Validate author name
    if (empty(trim($_POST["authorName"]))) {
        $authorName_err = "Please enter author name.";
    } else {
        $authorName = trim($_POST["authorName"]);
    }

    // Validate quantity
    if (empty(trim($_POST["quantity"]))) {
        $quantity_err = "Please enter quantity.";
    } else {
        $quantity = trim($_POST["quantity"]);
    }

    // Validate publication company
    if (empty(trim($_POST["publicationCompany"]))) {
        $publicationCompany_err = "Please enter publication company.";
    } else {
        $publicationCompany = trim($_POST["publicationCompany"]);
    }

    // Validate genre
    if (empty(trim($_POST["genre"]))) {
        $genre_err = "Please enter genre.";
    } else {
        $genre = trim($_POST["genre"]);
    }

    // Validate ISBN
    if (empty(trim($_POST["isbn"]))) {
        $isbn_err = "Please enter ISBN.";
    } else {
        $isbn = trim($_POST["isbn"]);
    }

    // Check if file is selected
    if ($_FILES["bookCover"]["error"] == 0) {
        $target_dir = "../uploads/books/";
        $original_filename = basename($_FILES["bookCover"]["name"]);
        $file_extension = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        $timestamp = time(); // Get current timestamp
        $new_filename = $timestamp . '_' . $original_filename; // Append timestamp to filename to make it unique
        $target_file = $target_dir . $new_filename;
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if file already exists
        if (file_exists($target_file)) {
            $uploadOk = 0;
            $bookCover_err = "Sorry, file already exists.";
        }

        // Check file size
        if ($_FILES["bookCover"]["size"] > 500000) {
            $uploadOk = 0;
            $bookCover_err = "Sorry, your file is too large.";
        }

        // Allow certain file formats
        if (
            $imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
            && $imageFileType != "gif"
        ) {
            $uploadOk = 0;
            $bookCover_err = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            $bookCover_err = "Sorry, your file was not uploaded.";
        } else {
            // Move the file to the target directory
            if (move_uploaded_file($_FILES["bookCover"]["tmp_name"], $target_file)) {
                $bookCover = $target_file;
            } else {
                $bookCover_err = "Sorry, there was an error uploading your file.";
            }
        }
    }

    // Check input errors before inserting into database
    if (
        empty($bookTitle_err) && empty($pagesNumber_err) && empty($authorName_err) && empty($quantity_err)
        && empty($publicationCompany_err) && empty($genre_err) && empty($isbn_err) && empty($bookCover_err)
    ) {

        // Prepare an insert statement
        $sql = "INSERT INTO book (bookTitle, pagesNumber, authorName, quantity, publicationCompany, genre, isbn, bookCover)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($conn, $sql)) {
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param(
                $stmt,
                "sisissss",
                $param_bookTitle,
                $param_pagesNumber,
                $param_authorName,
                $param_quantity,
                $param_publicationCompany,
                $param_genre,
                $param_isbn,
                $param_bookCover
            );

            // Set parameters
            $param_bookTitle = $bookTitle;
            $param_pagesNumber = $pagesNumber;
            $param_authorName = $authorName;
            $param_quantity = $quantity;
            $param_publicationCompany = $publicationCompany;
            $param_genre = $genre;
            $param_isbn = $isbn;
            $param_bookCover = $bookCover;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Redirect to book catalogue page
                header("location: ../bookCatalogueManage.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }

            // Close statement
            mysqli_stmt_close($stmt);
        }
    }

    // Close connection
    mysqli_close($conn);
}
