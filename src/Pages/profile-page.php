<?php
    session_start();
    ob_start();

    require_once(dirname(__DIR__)."\\PHP-Backend\\functions.php");

    //Creates the PDO connection
    try {
        //Creates a new PDO connection
        $db = getDatabaseConnection(); //Change to your database credentials
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Set default fetch mode to object
    } catch (PDOException $e){
        $_SESSION['err'] = $e;
        exit();
    }

    //Checks to see if the url contains a userid number
    if(isset($_GET['id'])){
        //If it does, sets the profile's userID to the id number in the URL
        $userID = $_GET['id'];
    } else if(isset($_SESSION['userID'])){
        //If there is no id number in the url, checks to see if the session's userID is set, and if it is sets the userID to the session's userID
        $userID = $_SESSION['userID'];
    } else {
        //If there is neither a session userID or a userID in the URL, redirects to the sign-in page
        header('Location: /src/Pages/sign-in-page.php');
    }

    //If the add friend button is pressed and there is a session userID, adds the session id and the get id to the friends table. Otherwise redirects to the sign-in page
    if(isset($_POST['add-friend'])){
        if(isset($_SESSION['userID'])){
            try{
                $you = $_SESSION['userID'];
                $friend = $_GET['id'];
                $query = "INSERT INTO friends VALUES ($you, $friend)";
                $result = $db->query($query);
                
            } catch (PDOException $e) {
                $echo =  "Connection failed: " . $e->getMessage();
            }
        } else {
            header('Location: /src/Pages/sign-in-page.php');
        }
    }  

    //If the add friend button is pressed and there is a session userID, removes the row from the friends table for the friends. Otherwise redirects to the sign-in page
    if(isset($_POST['remove-friend'])){
        if(isset($_SESSION['userID'])){
            try{
                $you = $_SESSION['userID'];
                $friend = $_GET['id'];
                $query = "DELETE FROM friends WHERE friend1 = $you AND friend2 = $friend";
                $result = $db->query($query);
                
            } catch (PDOException $e) {
                $echo =  "Connection failed: " . $e->getMessage();
            }
        } else {
            header('Location: /src/Pages/sign-in-page.php');
        }
    }

    //Finds the bio and display name and sets them equal to the bio and disName variables respectively
    $profileInfo = getProfileInfo($userID);

    if(isset($profileInfo)){
        $bio = $profileInfo['bio'];
        $disName = $profileInfo['disName'];
        $profPic = $profileInfo['profpicurl'];
    }


    //Finds the amount of friends a user has and sets it to the numFriends variable
    $numFriends = getCount("friends", "friend1",$userID);

    //Finds the amount of posts a user has made and sets it equal to the numPosts variable
    $numPosts = getCount("posts", "posterid", $userID);

    //Checks to see if the profile page to display is yours or if it is another user's
    if (!isset($_GET['id'])|| $_GET['id'] == $_SESSION['userID']){
        //If displaying your page, pulls all posts
        $uPosts = getPosts($userID, false);
    } else {
        //If displaying another user's page, displays only posts made in the last 24 hours
        $uPosts = getPosts($userID, true);
        $you = $_SESSION['userID'];
        $otherP = $_GET['id'];
        $isFriend = isFriend($you, $otherP);
    }

    //If the edit profile button is pressed, redirects to the edit-profile page
    if(isset($_POST['edit-profile'])){
        header('Location: /src/Pages/edit-profile-page.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
        //Checks to see if you are viewing your profile or another person's profile and updates the title respectively
        if(isset($_GET['id']) && $_GET['id'] != $_SESSION['userID']){
            echo "<title>$disName's Profile</title>";
        } else {
            echo "<title>Your Profile</title>";
        }
    ?>
    <link rel="stylesheet" href="/src/CSS/main.css">
    <link rel="stylesheet" href="/src/CSS/profile.css">
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
    
    <div class="profile-container">
        <!-- Profile Header Section -->
        <div class="profile-header">
            <?php echo "<img src='$profPic' alt='Profile Picture' id='profilePicture'>"?>
            
            <div class="profile-info">
                <?php
                    $postsTxt = "Posts";
                    $friendsTxt = "Friends";
                    $profileActionButton;

                    //Checks to see if there is only one friend/post and makes the text singular on the page
                    if($numPosts == 1){
                        $postsTxt = "Post";
                    }
                    if($numFriends == 1){
                        $friendsTxt = "Friend";
                    }

                    //Displays the user's information into the profile-header box
                    echo "<h1>$disName</h1>
                    <div class='stats'>
                        <span><b>$numPosts</b> $postsTxt</span>
                        <span><b>$numFriends</b> $friendsTxt</span>
                    </div>
                    
                    <p id='profileBio'>$bio</p>";

                    //Checks to see if the profile is yours, or another users
                    if($userID == $_SESSION['userID']){
                        //If the profile is yours, the profile action button is set to the edit-profile button
                        $profileActionButton = "name='edit-profile' value='Edit Profile'";

                    } else if ($isFriend == 1){
                        //If the profile is not yours, and you and the user you are viewing are friends the profile action button is set to the remove-friend button
                        $profileActionButton = "name='remove-friend' value='Remove Friend'";
                    } else {
                        //Otherwise, the profile action button is set to the add-friend button
                        $profileActionButton = "name='add-friend' value='Add Friend'";
                    }
                    
                    echo "<form action='' method='post'>";
                    echo "<input type='submit' class='profile-action-button' $profileActionButton>";
                    echo "</form>";
                ?>
            </div>
        </div>
        <h2>Recent Posts</h2>
    <div class="posts-area">
    <?php
        foreach($uPosts as $postInfo){
            $imgLink = $postInfo['imgurl'];
            $cap = $postInfo['caption'];
            echo 
            "<div class='post'>
                <img src='$imgLink' alt='Post Image'>
                <p>$cap</p>
                <div class='post-actions'>Like • Comment • Share</div> 
            </div>";
        }
    ?>
</div>

        
        
    </div>

<script src="/src/JS/darkMode.js"></script>
</body>
</html>
