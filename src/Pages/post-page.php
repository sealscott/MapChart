<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post</title>
    <link rel="stylesheet" href="/src/CSS/main.css">
    <link rel="stylesheet" href="/src/CSS/post.css">
    <link rel="icon" type="image/x-icon" href="/src/Icons/icon.png">
</head>

<body>
    <!-- Left Sidebar with Navigation Buttons -->
    <div id="sidebar">
    <button class="nav-button" id ="feedButton" onclick="location.href='/src/Pages/feed-page.php'"></button>
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
        <div id="app-title">MapChart</div>
        <input type="text" id="searchBox" placeholder="Find posts in any area">
        <button id="searchButton">Search</button>
    </div>

    <!--Form to post. UI could be updated-->
    <div class="post-container">
        <h2 class="post-header">Share Your Thoughts</h2>
        <form class="create-post-form" action="/src/PHP-Backend/create-post.php" method="post" enctype="multipart/form-data">
            <textarea class="caption" id="caption" name="caption" placeholder="What's on your mind?" rows="10" cols = "50"></textarea><br>
            <label for="post-image" class="image-label" name="postimg" id="postimg">Upload Image Here!</label><br>
            <input type="file" name="postimg" id="postimg" required>
            <button type="submit" class="submit-btn" name="sumbit-btn" id="submit-btn">Post!</button>

            <input type="hidden" name="lat" id="lat" value="">
            <input type="hidden" name="lon" id="lon" value="">
        </form>
    </div>

    <script>
    //attempts to get users location w/ browser geolocation
    navigator.geolocation.getCurrentPosition(function(position) {
      var userLat = position.coords.latitude;
      var userLon = position.coords.longitude;

      //set value to to hidden lat input to 4 decimal places of user lat
      var latInput = document.getElementById('lat');
      latInput.value = Number(userLat.toFixed(4));
      console.log(latInput.value); 

      var lonInput = document.getElementById('lon');
      lonInput.value = Number(userLon.toFixed(4));
      console.log(lonInput.value);
          
  },
  function () {//cannot get user location, display error message asking to enable browser location
      alert("Please enable location services in your browser settings to create a post");
  });

    </script>

    <script src="/src/JS/darkMode.js"></script>
</body>

