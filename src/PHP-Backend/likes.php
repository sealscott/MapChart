<?php
session_start();
require_once(__DIR__."\\functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getDatabaseConnection();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $likedpost = $_POST['postid']; // Post ID being liked/unliked
        $likerid = $_SESSION['userID']; // User ID of the liker/unliker
        $action = $_POST['action']; // Action: "like" or "unlike"

        if ($action === 'like') {
            // Check if the user has already liked the post
            $query = "SELECT COUNT(*) FROM likes WHERE likedpost = :likedpost AND likerid = :likerid";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':likedpost', $likedpost, PDO::PARAM_INT);
            $stmt->bindParam(':likerid', $likerid, PDO::PARAM_INT);
            $stmt->execute();
            $alreadyLiked = $stmt->fetchColumn();

            if ($alreadyLiked == 0) {
                // Insert the like into the likes table
                $query = "INSERT INTO likes (likedpost, likerid) VALUES (:likedpost, :likerid)";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':likedpost', $likedpost, PDO::PARAM_INT);
                $stmt->bindParam(':likerid', $likerid, PDO::PARAM_INT);
                $stmt->execute();
            }
        } elseif ($action === 'unlike') {
            // Remove the like from the likes table
            $query = "DELETE FROM likes WHERE likedpost = :likedpost AND likerid = :likerid";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':likedpost', $likedpost, PDO::PARAM_INT);
            $stmt->bindParam(':likerid', $likerid, PDO::PARAM_INT);
            $stmt->execute();
        }

        // Redirect back to the feed page
        header("Location: /src/Pages/feed-page.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>