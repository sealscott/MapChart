<?php
session_start();
ob_start();

require_once "../PHP-Backend/functions.php";

// Check if post ID is set
if (!isset($_GET['postid'])) {
    header('Location: /src/Pages/feed-page.php'); // Redirect to feed if no post ID
    exit();
}

$postID = $_GET['postid'];

try {
    $db = getDatabaseConnection(); // Change to your database credentials
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Fetch the post from the database based on post ID
    $query = "SELECT postid, caption, imgurl, posterid, posttime FROM posts WHERE postid = :postid AND posttime >= NOW() - INTERVAL 24 HOUR";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':postid', $postID, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch();

    if (!$post) {
        header('Location: /src/Pages/feed-page.php'); // Redirect if post not found
        exit();
    }

    // Fetch the display name of the user who posted the post
    $posterID = $post['posterid'];
    $poster = getDisplayName($posterID);

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

    // Fetch comments for the post
    $query = "SELECT body, commenttime, commenterid FROM comments WHERE postid = :postid ORDER BY commenttime ASC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':postid', $post['postid'], PDO::PARAM_INT);
    $stmt->execute();
    $comments = $stmt->fetchAll();
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
    <title>View Post</title>
    <link rel="stylesheet" href="/src/CSS/main.css">
    <link rel="stylesheet" href="/src/CSS/feed.css">
    <link rel="stylesheet" href="/src/CSS/view-post.css">
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

    <div id="map-header">
        <div id="app-title">MapChat</div>
    </div>

    <div id="content-container">
        <div id="feed">
            <!-- Display the full post -->
            <div class="post" id="post-<?php echo $post['postid']; ?>">
                <button class="back-button" onclick="window.location.href='/src/Pages/feed-page.php'">Back to Feed</button>
                <h3 class='post-username'><?php echo htmlspecialchars($poster); ?></h3>
                <?php
                // Display the post time in a human-readable format
                if (isset($post['posttime'])) {
                    $formattedTime = date('F j, Y, g:i a', strtotime($post['posttime']));
                } else {
                    $formattedTime = 'Time not available';
                }
                echo "<p class='post-time'>$formattedTime</p>";
                ?>
                <?php if (!empty($post['imgurl'])) : ?>
                    <img class="post-image" src="<?php echo htmlspecialchars($post['imgurl']); ?>" alt="Post Image">
                <?php endif; ?>
                <p><?php echo htmlspecialchars($post['caption']); ?></p>
                <div class="post-actions">
                    <!-- Like/Unlike Button -->
                    <form action="/src/PHP-Backend/likes.php" method="POST" class="like-form">
                        <input type="hidden" name="postid" value="<?php echo $post['postid']; ?>">
                        <input type="hidden" name="action" value="<?php echo $alreadyLiked ? 'unlike' : 'like'; ?>">
                        <button type="submit" class="like-button">
                            <?php echo $alreadyLiked ? 'Unlike' : 'Like'; ?>
                        </button>
                    </form>
                    <span class="like-count"><?php echo htmlspecialchars($likes); ?> Likes</span>
                    <!-- Comment Button -->
                    <form action="/src/PHP-Backend/comments.php" method="POST" class="comment-form">
                        <input type="hidden" name="postid" value="<?php echo $post['postid']; ?>">
                        <input type="text" name="comment" placeholder="Write a comment..." required>
                        <button type="submit" class="comment-button">Comment</button>
                    </form>
                </div>
                <!-- Display Comments -->
                <div class="comments-section">
                    <h4>Comments:</h4>
                    <?php if (!empty($comments)) : ?>
                        <?php foreach ($comments as $comment) : ?>
                            <?php
                            // Fetch the display name of the commenter
                            $commenterID = $comment['commenterid'];
                            $commenterName = getDisplayName($commenterID);
                            ?>
                            <div class="comment">
                                <p><strong><?php echo htmlspecialchars($commenterName); ?>:</strong> <?php echo htmlspecialchars($comment['body']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <p>No comments yet. Be the first to comment!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('feedButton').classList.add('active');
    </script>
    <script src="/src/JS/darkMode.js"></script>
</body>

</html>
