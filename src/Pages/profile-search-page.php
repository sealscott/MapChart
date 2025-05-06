<?php
session_start();
require_once(dirname(__DIR__) . "\\PHP-Backend\\functions.php");

// Initialize $searchRes to an empty array to avoid undefined variable errors.
$searchRes = array();

// Check if the search input exists.
if (isset($_POST['searchInput'])) {
    $search = $_POST['searchInput'];

    try {
        $db = getDatabaseConnection();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Use parameterized queries to prevent SQL injection.
        $query = "SELECT disName, uid FROM users WHERE disName LIKE :search ORDER BY disName LIKE :search_prefix DESC, disName ASC";
        $stmt = $db->prepare($query);
        $stmt->execute(['search' => '%' . $search . '%', 'search_prefix' => $search . '%']);
        $searchRes = $stmt->fetchAll();
    } catch (PDOException $e) {
        var_dump($e);
        exit();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Search</title>
    <link rel="stylesheet" href="/src/CSS/main.css">
    <link rel="stylesheet" href="/src/CSS/profile-search.css">
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

    <div class="profile-search-container">
        <h2 class="profile-search-header">Search Profiles</h2>
        <form id="profile-search-form" action="" method="POST">
            <input type="text" class="profile-search-bar" id="searchInput" name="searchInput" placeholder="Search by username" required>
            <button type="submit" id="profileSearchResultButton">Search</button>
        </form>
        
        <?php
        if (empty($search)) {
            echo "<p class = emptyResult>Search for Profiles!</p>";
        } else {
            if (!empty($searchRes)) { // If there are search results, display them
                foreach ($searchRes as $user) {
                    $uName = $user['disName'];
                    $pID = $user['uid'];
                    echo "<form method='post' action='profile-page.php?id=$pID'>
                            <input type='submit' class='search-res' value='$uName'>
                          </form>";
                }
            } else { // If a search was made but no results were found, show this message
                echo "<p class = emptyResult>No results found.</p>";
            }
        }
        
    
?>

    </div>

    <script src="/src/JS/darkMode.js"></script>
</body>
</html>
