<?php
session_start();
ob_start();

require_once(__DIR__."\\functions.php");

require(__DIR__ . "/config/config.php");

// Initialize error array
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Validate email
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format!";
    }

    // Validate password
    if (isset($_POST['password'])) {
        $temppass = $_POST['password'];
        $validpass = preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}/', $temppass); // At least 8 characters, 1 letter and 1 number

        if (!$validpass) {
            $errors['password'] = "Password must have 8 characters, 1 letter and 1 number!";
        }
    }

    // Validate password match
    if ($_POST['password'] != $_POST['confirm-password']) {
        $errors['confirm_password'] = "Passwords do not match!";
    }

    // If there are no errors, proceed with registration
    if (empty($errors)) {
        try {

            //Creates a new database connection and sets the PDO attributes
            $db = getDatabaseConnection();
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $dsn = "mysql:host=". $config['DB_HOST'] .";dbname=" . $config['DB_NAME'] . ";port=" . $config['PORT'];
            $options = [PDO::MYSQL_ATTR_SSL_CA => $config['SSLCA'], PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false];

            $db = getDatabaseConnection();

            //Sets the username, password, and display name to be used in the SQL query
            $username = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $disName = $_POST['display-name'];

            //Inserts the username, password, and display name to the user table
            $query = "INSERT INTO users (username, upass, disName) VALUES ('$username', '$password', '$disName')";
            $stmt = $db->prepare($query);
            $stmt->execute();

            //Finds the userid for the newly created user sets it to the session's userID variable
            $query = "SELECT uid FROM users WHERE username = '$username'";
            $stmt2 = $db->prepare($query);
            $stmt2->execute();
            $userID = $stmt2->fetchColumn();
            $_SESSION['userID'] = $userID;

            header('Location: /src/Pages/feed-page.php'); // Redirect to the feed page after successful registration
            exit();

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit();
        }
    } else {
        // Store errors in session
        $_SESSION['register_errors'] = $errors;
        header('Location: /src/Pages/register-page.php'); // Redirect to the register page with errors
        exit();
    }
} else {
    // If the form is not submitted, redirect to the register page
    header('Location: /src/Pages/register-page.php');
    exit();
}

?>