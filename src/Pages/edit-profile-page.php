<?php
    session_start();
    ob_start();

    require_once(dirname(__DIR__)."\\PHP-Backend\\functions.php");

    $userID = $_SESSION['userID'];

    $profileInfo = getProfileInfo($userID);

    if ($profileInfo != false){
        $disName = $profileInfo['disName'];
        $bio = $profileInfo['bio'];
    }
?>

<!DOCTYPE html
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="/src/CSS/main.css">
    <link rel="stylesheet" href="/src/CSS/profile.css">
    <link rel="stylesheet" href="/src/CSS/edit-profile.css">
    <link rel="icon" type="image/x-icon" href="/src/Icons/icon.png">
</head>

<body>
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
    
    
    <div id="map-header">
        <div id="app-title">MapChat</div>
        <input type="text" id="searchBox" placeholder="Find posts in any area">
        <button id="searchButton">Search</button>
    </div>
    
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-info">
                <!--Form to edit profile information. UI could be updated-->
                <form class="update-form" id="edit-profile-form" action="/src/PHP-Backend/update-profile.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="profpic" id="profpic">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo $disName; ?>" required>

                    <label for="bio">Bio:</label>
                    <textarea class="bio-editor" id="bio" name="bio" value="<?php echo $bio ?>" ><?php echo $bio ?></textarea>

                    <div class="save-changes-button-container">
                        <button class="save-changes-button" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="/src/JS/darkMode.js"></script>
</body>
</html>
