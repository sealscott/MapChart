<?php
session_start();
require_once("functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = getDatabaseConnection();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $postid = $_POST['postid'];
        $comment = htmlspecialchars($_POST['comment']);
        $commenterid = $_SESSION['userID']; // Updated to match `commenterid`

        // Insert the comment into the comments table
        $query = "INSERT INTO comments (postid, commenterid, body, commenttime) VALUES (:postid, :commenterid, :body, NOW())";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':postid', $postid, PDO::PARAM_INT);
        $stmt->bindParam(':commenterid', $commenterid, PDO::PARAM_INT);
        $stmt->bindParam(':body', $comment, PDO::PARAM_STR);
        $stmt->execute();

        // Redirect back to the feed page
        header("Location: /src/Pages/feed-page.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>