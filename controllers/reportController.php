<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    $action = $_GET['action'];
    switch ($action) {
        case 'bookPopularity':
            echo json_encode(getBookPopularity());
            break;
        case 'borrowingDataOverTime':
            echo json_encode(getBorrowingDataOverTime());
            break;
        case 'mostPopularBooks':
            echo json_encode(getMostPopularBooks());
            break;
        default:
            echo json_encode([]);
            break;
    }
}

function getBookPopularity() {
    global $conn;
    $sql = "SELECT book.bookTitle, COUNT(borrow.bookID) AS borrow_count
            FROM borrow
            JOIN book ON borrow.bookID = book.id
            GROUP BY borrow.bookID
            ORDER BY borrow_count DESC";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getBorrowingDataOverTime() {
    global $conn;
    $sql = "SELECT HOUR(time) AS hour, COUNT(*) AS borrow_count
            FROM borrow
            GROUP BY hour
            ORDER BY hour";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

function getMostPopularBooks() {
    global $conn;
    $sql = "SELECT book.bookTitle, COUNT(borrow.bookID) AS borrow_count
            FROM borrow
            JOIN book ON borrow.bookID = book.id
            GROUP BY borrow.bookID
            ORDER BY borrow_count DESC
            LIMIT 10";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>
