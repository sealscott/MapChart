<?php
//darkmode toggle
$darkMode = '';
if (isset($_COOKIE['darkMode']) && $_COOKIE['darkMode'] === 'enabled') {
    $darkMode = 'dark-mode';
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <body class="<?php echo $darkMode; ?>">
    <title>WeatherMap Social</title>

    <link id="mainStylesheet" rel="stylesheet" href="/src/CSS/main.css">
    <link id="mapStylesheet"rel="stylesheet" href="/src/CSS/map.css">
    <link rel="icon" type="image/x-icon" href="/src/Icons/icon.png">
    
    <!-- Leaflet CSS Import -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>

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
    
    <!-- Weather Widget -->
    <div id="weather-widget">
        <div id="weather-header">
            <div id="weather-title">Weather Info</div>
            <button id="weather-toggle">+</button>
        </div>
        <div id="weather-content">
            <div id="weather-location">Location</div>
            <img id="weather-icon" src="" alt="Weather icon">
            <div id="weather-temp">--°C</div>
            <div id="weather-description">Weather description</div>
            <div id="weather-details">
                <div class="weather-detail">
                    <span>Feels like:</span>
                    <span id="weather-feels">--°C</span>
                </div>
                <div class="weather-detail">
                    <span>Humidity:</span>
                    <span id="weather-humidity">--%</span>
                </div>
                <div class="weather-detail">
                    <span>Wind:</span>
                    <span id="weather-wind">-- km/h</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container (90% of screen width) -->
    <div id="map-container">
        <!-- Header inside map with title and search -->
        <div id="map-header">
            <div id="app-title">WeatherMap Social</div>
            <input type="text" id="searchBox" placeholder="Find posts in any area">
            <button id="searchButton">Search</button>
        </div>
        
        <!-- The map -->
        <div id="map"></div>
    </div>


    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
    
    <script src="/src/JS/map.js"></script>
    <script src="/src/JS/darkMode.js"></script>

    
</body>
</html>