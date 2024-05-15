<?php
// Start the session
session_start();

// Initialize error variable
$error = "";

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if username and password are set and not empty
    if (isset($_POST['username']) && isset($_POST['password']) && !empty($_POST['username']) && !empty($_POST['password'])) {
        // Sanitize user input to prevent SQL injection
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        // Database connection parameters
        $servername = "localhost";
        $db_username = "root";
        $db_password = ""; // Assuming no password for the root user
        $dbname = "bbms";

        try {
            // Create a new PDO instance
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);

            // Set the PDO error mode to exception
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare SQL statement to retrieve user data
            $stmt = $pdo->prepare("SELECT * FROM user WHERE username = :username AND password = :password");

            // Bind parameters
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);

            // Execute the prepared statement
            $stmt->execute();

            // Fetch user data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Check if a user with the provided credentials exists
            if ($stmt->rowCount() == 1) {
                // Set session variables
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $username;
                $_SESSION["id"] = $user['id'];
                $_SESSION["role"] = $user['role']; // Assuming 'role' is the column name in your database

                // Redirect to home page
                header("location: ../homepage.php");
                exit;
            } else {
                // Incorrect username or password
                $error = "Invalid username or password";
            }
        } catch (PDOException $e) {
            // Display error message
            die("Error: " . $e->getMessage());
        }

        // Close connection
        $pdo = null;
    } else {
        // Username or password is not set or empty
        $error = "Please enter both username and password";
    }
}

// Pass the error variable to the login page
$_SESSION["error"] = $error;

// Redirect back to login page
header("location: ../login.php");
exit;
