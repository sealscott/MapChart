<?php
    session_start(); 

    require_once "../PHP-Backend/functions.php";

    function return_to_feed (){
        header("Location: /src/Pages/feed-page.php");
        exit();
    }

    try{
        $db = getDatabaseConnection(); //Change to your database credentials
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Set default fetch mode to object
    } catch (PDOException $e){

    }

    //Ensures that the message is only viewable to the two people sending messages to each other
    if(isset($_GET['u']) && isset($_GET['f'])){
        if($_SESSION['userID']==$_GET['u']){
            $canMessage = isFriend($_GET['u'], $_GET['f']);

            if($canMessage == 0){
                return_to_feed();
            }
        } else {
            return_to_feed();
        }
    } else {
        return_to_feed();
    }

    if(isset($_POST['send-message'])){
        try{
            //When send message button is pressed, inserts the sender id, reciever id, and the message to the messages table to be held
            $query = "INSERT INTO messages (sid, rid, message) VALUES (:sid, :rid, :message)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":sid", $_SESSION['userID']);
            $stmt->bindParam(":rid", $_GET['f']);
            $stmt ->bindParam(":message", $_POST['message']);
            $stmt->execute();
        } catch (PDOException $e){
            print_r($e);
            exit();
        }
    }

    try {
        //If they can message each other pull the display name of the person you are sending messages to and pull up all messages
        $friendName = getDisplayName($_GET['f']);

        $query = "SELECT message, sid FROM messages WHERE (sid= :you AND rid = :friend) OR (sid = :friend AND rid = :you)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(":you", $_SESSION['userID']);
        $stmt->bindParam(":friend", $_GET['f']);
        $stmt->execute();
        $messages = $stmt->fetchAll();
    } catch(PDOException $e){

    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages: <?php echo $friendName ?></title>
    <link rel="stylesheet" href="/src/CSS/main.css">
    <link rel="stylesheet" href="/src/CSS/messages.css">
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

    <div class="messages-container">
            <?php
                echo "<h3 class='messages-header'>$friendName</h3>";
                foreach($messages as $message){
                    $messageContents = $message['message'];
                    if($message['sid'] == $_SESSION['userID']){
                        echo "<div class='message-sent'>";
                        echo "<p class='message-contents'>$messageContents</p>";
                        echo "</div>";
                    } else {
                        echo "<div class='message-recieved'>";
                        echo "<p class='message-contents'>$messageContents</p>";
                        echo "</div>";
                    }
                }
            ?>
    </div>

    <div class="send-message-container">
        <form id="send-message" action="" method="POST">
            <input type="text" class="message-bar" name="message" required>
            <input type="submit" class="message-button" name="send-message" value="Send Message">
        </form>
    </div>
</body>
