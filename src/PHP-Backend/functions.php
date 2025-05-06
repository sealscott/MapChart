<?php

    /*************************************************************************************************************************************************//**
    * @name checkIllegalWords
    * @brief Checks a caption for illegal words using a filter file "contentFilter.txt"
    * @param $caption The caption to check for illegal words
    * @param $filterPath The path to the filter file
    * @return string|null Returns the illegal word if found, otherwise null
    *****************************************************************************************************************************************************/

    function checkIllegalWords($caption, $filterPath) {
        if (!file_exists($filterPath)) {
            throw new Exception("Content filter file not found: $filterPath");
        }
    
        $illegalWords = file($filterPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
        foreach ($illegalWords as $word) {
            if (stripos($caption, $word) !== false) {
                return $word;
            }
        }
    
        return null;
    }

    /*************************************************************************************************************************************************//**
    * @name getCount
    * @brief Returns the number of rows in a table with a specified value held in a column
    * @param $table The table to search
    * @param $column The column to search in
    * @param $id The identifier to filter by
    * @return int The number of rows found in the table
    *****************************************************************************************************************************************************/

    function getCount($table, $column, $id) {
        try {
            $db = getDatabaseConnection();
            $query = "SELECT COUNT(*) FROM $table WHERE $column = :id";
            $stmt = $db->prepare($query);
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchColumn();
            return $result;
        } catch (PDOException $e) {
            print_r($e);
            exit();
        }
    }

    /*************************************************************************************************************************************************//**
    * @name getProfileInfo
    * @brief Returns the profile information of a user
    * @param $id The id number to search for
    * @return array An array that contains the bio, display name, and profile picture url
    *****************************************************************************************************************************************************/

    function getProfileInfo($id){
        try{
            $db = getDatabaseConnection();
            $query = "SELECT bio, disName, profpicurl FROM users WHERE uid = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch();

            if ($result === false) {
                return []; // No data found
            }

            return $result;
        } catch (PDOException $e){
            error_log($e->getMessage());
            throw new Exception("Failed to fetch profile info.");
        }
    }

    /*************************************************************************************************************************************************//**
    * @name getDisplayName
    * @brief Returns the display name of a user
    * @param $id The id number to search for
    * @return string The value held in the disName table for that user
    *****************************************************************************************************************************************************/

    function getDisplayName($id){
        try{
            $db = getDatabaseConnection();
            $query = "SELECT disName FROM users WHERE uid = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchColumn();

            if ($result === false) {
                return null; // No data found
            }

            return $result;
        } catch (PDOException $e){
            error_log($e->getMessage());
            throw new Exception("Failed to fetch display name.");
        }
    }

    /*************************************************************************************************************************************************//**
    * @name getPosts
    * @brief Returns post information 
    * @param $lastDay A boolean that will notify if only posts made in the last day are wanted
    * @param $id The post id number to search for
    * @return array An array holding the caption and image url for the post
    *****************************************************************************************************************************************************/

    function getPosts($id, $lastDay){
        $query = $lastDay
        ? "SELECT caption, imgurl FROM posts WHERE posterid = :id AND posttime >= NOW() - INTERVAL 1 DAY ORDER BY posttime DESC"
        : "SELECT caption, imgurl FROM posts WHERE posterid = :id";

        try{
            $db = getDatabaseConnection();
            $stmt = $db->prepare($query);
            $stmt-> bindParam(":id", $id, PDO::PARAM_INT);
            $stmt-> execute();
            $result = $stmt->fetchAll();

            if ($result === false || empty($result)) {
                return []; // No posts found
            }

            return array_reverse($result); // Reverse the order of the posts
        } catch (PDOException $e){
            error_log($e->getMessage());
            throw new Exception("Failed to fetch posts.");
        }
    }

    /*************************************************************************************************************************************************//**
    * @name getComments
    * @brief Retrieves the comments for a post
    * @param $id The id number for the post
    * @return array|bool Returns the commenterid and comment body for all comments to a post
    *****************************************************************************************************************************************************/
    
    function getComments($id){
        try {
            $db = getDatabaseConnection();
            $query = "SELECT commenterid, body, commenttime FROM comments WHERE postid = :id ORDER BY commenttime ASC";
            $stmt = $db->prepare($query);
            $stmt->bindParam(":id", $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e){
            error_log($e->getMessage());
            return false;
        }
    }
    
    
    

    /*************************************************************************************************************************************************//**
    * @name isFriend
    * @brief Determines if two users are friends
    * @param $id1 The id number for friend1
    * @param $id2 The id number for friend2
    * @return int Returns 1 if users are friends, and 0 if users are not friends
    *****************************************************************************************************************************************************/
    
    function isFriend($id1, $id2){
        try{
            $db = getDatabaseConnection();
            $query = "SELECT COUNT(*) FROM friends WHERE friend1 = :id1 AND friend2 = :id2";
            $stmt = $db->prepare($query);
            $stmt-> bindParam(":id1", $id1);
            $stmt-> bindParam(":id2", $id2);
            $stmt-> execute();
            $result = $stmt->fetchColumn();
            return $result;
        } catch (PDOException $e){
            error_log($e->getMessage());
            throw new Exception("Failed isFriend.");
        }
    }

    /*************************************************************************************************************************************************//**
    * @name isLiked
    * @brief Determines if a user has liked the post
    * @param $postid The id number for the post
    * @param $userid The id number for the user
    * @return int Returns 1 if users has liked the post, and 0 if users has not
    *****************************************************************************************************************************************************/

    function isLiked($postid, $userid){
        try{
            $db = getDatabaseConnection();
            $query = "SELECT COUNT(*) FROM likes WHERE likedpost = :postid AND likerid = :userid";
            $stmt = $db->prepare($query);
            $stmt-> bindParam(":postid", $postid);
            $stmt-> bindParam(":likerid", $userid);
            $stmt-> execute();
            $result = $stmt->fetchColumn();
            return $result;
        } catch (PDOException $e){
            error_log($e->getMessage());
            throw new Exception("Failed isLiked.");
        }
    }

    function getDatabaseConnection() {
        $config = require('../PHP-Backend/config/config.php'); // Load the configuration file
    
        // if (!isset($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME'])) {
        //     throw new Exception("Database configuration is incomplete.");
        // }

        // if (!isset($config['DB_HOST'], $config['DB_USER'], $config['DB_PASS'], $config['DB_NAME'])) {
        //     var_dump($config); // Debugging: Output the config array
        //     throw new Exception("Database configuration is incomplete.");
        // }

        try {
            // Setup DSN with SSL mode
            $dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};charset=utf8mb4";
    
            // Set PDO options, including SSL
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ];
    
            // If SSL is required, add SSL options
            if ($config['DB_SSL'] === 'require') {
                $options[PDO::MYSQL_ATTR_SSL_CA] = __DIR__ . '/config/DigiCertGlobalRootCA.crt.pem'; // Path to the SSL certificate
            }
    
            // Create PDO instance
            $db = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], $options);
            return $db;
    
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
            exit();
        }
    }
    
    
?>