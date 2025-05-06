<?php
    require_once "../PHP-Backend/functions.php";

    session_start();
    ob_start(); 
    $err = ""; // Initialize a variable to store error messages 

    // Check if the form has been submitted (via POST request)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Connect to the database
            $db = getDatabaseConnection();
            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Enable error reporting for database operations

            // Prepare a query to fetch the user ID and hashed password for the entered username
            $query = "SELECT uid, upass FROM users WHERE username = :username";
            $stmt = $db->prepare($query); // Prepare the SQL query for execution
            $stmt->bindParam(':username', $_POST["username"]); // Bind the entered username to the query
            $stmt->execute(); // Execute the query
            $result = $stmt->fetch(PDO::FETCH_OBJ); // Fetch the result as an object

            // Check if a user with the entered username exists in the database
            if ($result) {
                $upass = $result->upass; // Get the hashed password from the database

                // Verify if the entered password matches the hashed password in the database
                if (password_verify($_POST['password'], $upass)) {
                    // If the passwords match, set session variables for the user
                    $_SESSION['userID'] = $result->uid; // Store the user ID in the session
                    $_SESSION['timeout'] = time() + 3600; // Set a timeout for the session (1 hour)

                    // Redirect the user to the map page
                    header('Location: /src/Pages/map-page.php');
                    exit(); // Stop further script execution after the redirect
                } else {
                    // If the passwords don't match, set an error message
                    $err = "Invalid username or password. Please try again.";
                }
            } else {
                // If no user is found with the entered username, set an error message
                $err = "Invalid username or password. Please try again.";
            }
        } catch (PDOException $e) {
            // If there is a database connection error, set an error message
            $err = "Connection failed: " . $e->getMessage();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-in</title>
    <link rel="stylesheet" href="/src/CSS/sign-in.css">
    <!-- <link rel="stylesheet" href="/CSS/main.css"> DOESNT NEED TO GO TO MAIN ( BODYS COLLIDE )-->
    <link rel="icon" type="image/x-icon" href="/src/Icons/icon.png">
</head>

<body>
    <header>
        <h1 id= "title">MapChat</h1>
    </header>

    <section>
        <form action="" method="post" name='login'>  <!-- Add action option to connect w/ backend-->
            <div class="form-group">
                <input placeholder="Email" type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <input placeholder="Password" type="password" id="password" name="password" required>
            </div>
            <button type="submit" value="submit" name="submit">Sign In</button>
        </form>
    </section>

    <div>
        <?php
            //Checks to see if there is an error message and displays it
            if ($err != ""){
                echo "<p class='error'>$err</p>";
            }
        ?>
    </div>

    <section>
        <div class="registerWidget">
            <p>Don't have an account yet? Create one here: <a href="/src/Pages/register-page.php">Register</a></p>
        </div>
    </section>
</body>
</html>
