<?php
    session_start();
    ob_start();
    $err = "";
    require_once dirname(__DIR__)."\\PHP-Backend\\functions.php";

    try {
        $db = getDatabaseConnection(); // Change to your database credentials
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Set default fetch mode to object

        // Fetch posts from the database
        $query = "SELECT postid, caption, imgurl, posterid, posttime FROM posts WHERE posttime >= NOW() - INTERVAL 1 DAY ORDER BY posttime DESC";

        $stmt = $db->prepare($query);
        $stmt->execute();
        $posts = $stmt->fetchAll();

    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feed</title>
    <link rel="stylesheet" href="/src/CSS/main.css">
    <link rel="stylesheet" href="/src/CSS/feed.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
    <link rel="icon" type="image/x-icon" href="/src/Icons/icon.png">
    <style>
        
    </style>
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
    

    <div id="map-header">
        <div id="app-title">MapChat</div>
        <input type="text" id="searchBox" placeholder="Find posts in any area">
        <button id="searchButton">Search</button>
    </div>

    <div id="content-container">
        <div id="feed">
            <!-- Creates a post container for each post made in the last day and displays all the post information -->
            <?php foreach ($posts as $post): ?>
                <div class="post" id="post-<?php echo $post['postid']; ?>">
                    <?php
                            // Finds the display name of the user who posted the post and displays it to the screen
                        $posterID = $post['posterid'];
                        $poster = getDisplayName($posterID);
                        
                        echo "<h3 class='post-username'>$poster</h3>";

                        // Display the post time in a human-readable format
                        // Assuming $post['posttime'] is in a format that strtotime can parse
                        if (isset($post['posttime'])) {
                            $formattedTime = date('F j, Y, g:i a', strtotime($post['posttime']));
                        } else {
                            $formattedTime = 'Time not available';
                        }
                        echo "<p class='post-time'>$formattedTime</p>";
                        

                        // Fetch the number of likes for the post
                        $query = "SELECT COUNT(*) FROM likes WHERE likedpost = :postid";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':postid', $post['postid'], PDO::PARAM_INT);
                        $stmt->execute();
                        $likes = $stmt->fetchColumn();

                        // Check if the user has already liked the post
                        $query = "SELECT COUNT(*) FROM likes WHERE likedpost = :postid AND likerid = :likerid";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':postid', $post['postid'], PDO::PARAM_INT);
                        $stmt->bindParam(':likerid', $_SESSION['userID'], PDO::PARAM_INT);
                        $stmt->execute();
                        $alreadyLiked = $stmt->fetchColumn();

                        $query = "SELECT body, commenttime, commenterid FROM comments WHERE postid = :postid ORDER BY commenttime ASC";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':postid', $post['postid'], PDO::PARAM_INT);
                        $stmt->execute();
                        $comments = $stmt->fetchAll();
                      
                        //add marker for post location to map
                        $query = "SELECT lat, lon FROM posts WHERE postid = :postid";
                        $stmt = $db->prepare($query);
                        $stmt->bindParam(':postid', $post['postid'], PDO::PARAM_INT);
                        $stmt->execute();
                        $row = $stmt->fetch(PDO::FETCH_ASSOC); // Fetch the row as an associative array

                        $lat = $row['lat'];
                        $lon = $row['lon'];
                    
                    ?>
                    
                    <!-- Creates a JS object for accessing values in feed.js -->
                    <script type="module">
                        import {displayPostLocation} from'/src/JS/feed.js';

                        feed.postLocation = {

                            postID: "post-<?php echo $post['postid'] ?>",
                            lat: <?php echo $lat; ?>,
                            lon: <?php echo $lon; ?>

                        };

                        displayPostLocation();

                    </script>
                    
                    <?php if (!empty($post['imgurl'])): ?>
                        <img class="post-image" src="<?php echo htmlspecialchars($post['imgurl']); ?>" alt="Post Image">
                    <?php endif; ?>
                    <p class="post-<?php echo $post['postid']; ?>-caption" ><?php echo$post['caption']; ?></p>
                    <div class="post-actions">
                        <!-- Like/Unlike Button -->
                        <form action="/src/PHP-Backend/likes.php" method="POST" class="like-form">
                            <input type="hidden" name="postid" value="<?php echo $post['postid']; ?>">
                            <input type="hidden" name="action" value="<?php echo $alreadyLiked ? 'unlike' : 'like'; ?>">
                            <button type="submit" class="like-button" onclick="storePostId(<?php echo $post['postid']; ?>)">
                                <?php echo $alreadyLiked ? 'Unlike' : 'Like'; ?>
                            </button>
                        </form>
                        <span class="like-count"><?php echo $likes; ?> Likes</span>
                        <!-- Comment Button -->
                        <form action="/src/PHP-Backend/comments.php" method="POST" class="comment-form" onsubmit="storePostId(<?php echo $post['postid']; ?>)">
                            <input type="hidden" name="postid" value="<?php echo $post['postid']; ?>">
                            <input type="text" name="comment" placeholder="Write a comment..." required>
                            <button type="submit" class="comment-button">Comment</button>
                        </form>
                        <!-- View Post Button -->
                        <form action="/src/Pages/view-post.php" method="GET" class="view-post-form">
                            <input type="hidden" name="postid" value="<?php echo $post['postid']; ?>">
                            <button type="submit" class="view-post-button">View Post</button>
                        </form>
                    </div>
                    <!-- Display Comments -->
                    <div class="comments-section">
                        <h4>Comments:</h4>
                        <?php if (!empty($comments)): ?>
                            <?php foreach ($comments as $comment): ?>
                                <?php
                                    // Fetch the display name of the commenter
                                    $commenterID = $comment['commenterid'];
                                    $commenterName = getDisplayName($commenterID);
                                ?>
                                <div class="comment">
                                    <p><strong><?php echo ($commenterName); ?>:</strong> <?php echo $comment['body']; ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No comments yet. Be the first to comment!</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>




        <div id="map-container">
            <div id="map"></div>
        </div>


    </div>
</div>
    <script> document.getElementById('feedButton').classList.add('active'); </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="/src/JS/feed.js" type="module"></script>
    
    <script>
             
            // Add event listener to search button
            document.getElementById("searchButton").addEventListener("click", searchLocation);
            
            // Allow searching by pressing Enter key in search box
            document.getElementById("searchBox").addEventListener("keypress", function(event) {
                if (event.key === "Enter") {
                    searchLocation();
                }
            });
        
        // Function to search for a location
        function searchLocation() {
            const address = document.getElementById("searchBox").value;
            
            if (!address) {
                return; // Don't search if input is empty
            }
            
            geocoder.geocode({ address: address }, function(results, status) {
                if (status === "OK") {
                    // Get the coordinates of the found location
                    const location = results[0].geometry.location;
                    
                    // Center the map on the found location
                    map.setCenter(location);
                    map.setZoom(14); // Zoom in a bit
                    
                    // Add a marker at the found location
                    if (marker) {
                        marker.setMap(null); // Remove existing marker
                    }
                    
                    marker = new google.maps.Marker({
                        map: map,
                        position: location,
                        animation: google.maps.Animation.DROP
                    });
                } else {
                    // Alert if location not found
                    alert("Location not found: " + status);
                }
            });
        }

        //this js logic is used to be able to scroll back to a post after a like or comment
        function storePostId(postId) {
            sessionStorage.setItem('scroll_to_post_id', postId);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const postId = sessionStorage.getItem('scroll_to_post_id');
            if (postId) {
                const element = document.getElementById('post-' + postId);
                if (element) {
                    element.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
                sessionStorage.removeItem('scroll_to_post_id');
            }
        });
    </script>
    
    
    <script>
        document.getElementById('feedButton').classList.add('active');
    </script>
    <script src="/src/JS/darkMode.js"></script>
</body>
</html>
