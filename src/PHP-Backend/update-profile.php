<?php
    session_start();
    ob_start();
    require_once(__DIR__."\\functions.php");

    if(isset($_FILES["profpic"]) && $_FILES["profpic"]["error"] === UPLOAD_ERR_OK){

        /*Sets the target directory, determines the file type, sets the img name to the current time and concatinates it with a . and the file type,
        then sets the target file to the combination of the target directory and image name.*/
        $targetDir = "Profile-Pics/";
        $imageFileType = strtolower(pathinfo($_FILES["profpic"]["name"], PATHINFO_EXTENSION));
        $imgName = time() . "." . $imageFileType;
        $targetFile = $targetDir . $imgName;

        if(!file_exists($targetDir)){
            mkdir($targetDir, 0777);
        }

        //Gets the temp folder directory, gets the tmp file name, and combines them with a directory separator and sets it to the tempPath
        $tmpDir = sys_get_temp_dir();
        $tmpName = basename($_FILES['profpic']['tmp_name']);
        $tempPath = $tmpDir . DIRECTORY_SEPARATOR . $tmpName;
        
        //Ensures that the file type is an approved type: PNG, JPEG, JGP, GIF
        if ($imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "jpg" && $imageFileType !="gif"){
            $err = "Image must be a JPG, JPEG, PNG, or GIF.";
        }
        
        //Moves the file from the temp directory, renames it, and places it in the target directory. If the file cannot be moved, it prints an error screen
        if (!move_uploaded_file($tempPath, $targetFile)){
            echo "File could not be uploaded";
            exit();
        }
    }

    try{
        //Creates a new PDO connection and sets attributes
        $db = getDatabaseConnection();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        //Gets information from the session and form to send to the 
        $userID = $_SESSION['userID'];
        $bio = $_POST['bio'];
        $disName = $_POST['username'];
        
        if($_FILES["profpic"]['name'] != ""){
            $query = "UPDATE users SET bio = '$bio', disName = '$disName', profpicurl = '/src/PHP-Backend/$targetFile' WHERE uid = $userID";
            $stmt = $db->query($query);
        } else {
        //Udates the user's bio and display name in the user table
            $query = "UPDATE users SET bio = '$bio', disName = '$disName' WHERE uid = $userID";
            $stmt = $db->query($query);
        }

    } catch (PDOException $e) {
        echo  "Connection failed: " . $e->getMessage();
        exit();
    }

    header('Location: /src/Pages/profile-page.php'); // Redirect to the profile page after updating
?>
