<?php
    session_start();

    $userID = $_SESSION['userID'];

    require_once(dirname(__DIR__)."\\PHP-Backend\\functions.php");

    try {
        //Creates a new PDO connection
        $db = getDatabaseConnection(); //Change to your database credentials
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Set default fetch mode to object

        $query = "SELECT friend2 FROM friends WHERE friend1 = :userID";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":userID", $userID);
        $stmt->execute();
        $friends = $stmt->fetchAll();

    } catch (PDOException $e){
        $_SESSION['err'] = $e;
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Friends</title>
    <link rel="stylesheet" href="/src/CSS/main.css">
    <link rel="stylesheet" href="/src/CSS/friends-page.css">
    <link rel="icon" type="image/x-icon" href="/src/Icons/icon.png">
</head>

<body>
    <!-- Left Sidebar with Navigation Buttons -->
    <div id="sidebar">
        <button class="nav-button" id ="feedButton" onclick="location.href='feed-page.php'"></button>
        <button class="nav-button" id="mapButton" onclick="location.href='map-page.php'"></button>
        <button class="nav-button" id ="profileButton" onclick="location.href='profile-page.php'"></button>
        <button class="nav-button" id ="friendsButton" onclick="location.href='friends-page.php'"></button>
        <button class="nav-button" id="profileSearchButton" onclick="location.href='profile-search-page.php'"></button>
        <button class="nav-button" id="postButton" onclick="location.href='post-page.php'"></button>
        <button class="nav-button" id="settingsButton" onclick="location.href='settings-page.php'"></button>
        <button class="nav-button" id="signOutButton" onclick="location.href='/src/PHP-Backend/sign-out.php'"></button>
        <!-- Add this button to your sidebar after the Sign Out button -->
        <button class="nav-button" id="darkModeButton"></button>
</div>
    

    <!--Need to add functionality for the search bar on this page.-->
    <div id="map-header">
        <div id="app-title">MapChat</div>
        <input type="text" id="searchBox" placeholder="Find posts in any area">
        <button id="searchButton">Search</button>
    </div>

    <div class="friends-container">

        <h3 class="friends-header">Friends</h3>

        <?php
            foreach($friends as $friend){

                //Gets the id of the friend and retrieves their display name
                $friendID = $friend['friend2'];
                $friendName = getDisplayName($friendID);

                //Displays the friend's name with two buttons, one to view the profile and another to view messages
                echo "<div class='friend-container'>";
                echo "<h3 class='friend-name'>$friendName</h3>";
                echo "<form class='friend-info' method='post' action='profile-page.php?id=$friendID'>";
                echo "<input type='submit' class='view-prof' value='View Profile'>";
                echo "</form>";
                echo "<form class='friend-info' method='post' action='messages-page.php?u=$userID&f=$friendID'>";
                echo "<input type='submit' class='view-mess' value='View Messages'>";
                echo "</form>";
                echo "</div>";
            }
        ?>
    </div>

    <script src="/src/JS/darkMode.js"></script>

</body>
