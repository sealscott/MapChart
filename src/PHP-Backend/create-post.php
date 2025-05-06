<?php
    session_start();

    $userID;
    $caption;

    require_once("functions.php");
    
    $lat = null;
    $lon = null;
    
    //check lat & lon for valid values
    if(isset($_POST["lat"])){
        $lat = $_POST['lat'];
    } else {
        echo "Error: " . $e->getMessage();
        exit();
    }

    if(isset($_POST['lon'])){
        $lon = $_POST['lon'];
    } else {
        echo "Error: " . $e->getMessage();
        exit();
    }

    // Load contentFilter
    $filterFile = "/contentFilter/contentFilter.txt";

    //Ensures that an image is submitted to the post and that there are no file upload errors
    if(isset($_FILES["postimg"]) && $_FILES["postimg"]["error"] === UPLOAD_ERR_OK){

        /*Sets the target directory, determines the file type, sets the img name to the current time and concatinates it with a . and the file type,
        then sets the target file to the combination of the target directory and image name.*/
        $targetDir = "Post-Images/";
        $imageFileType = strtolower(pathinfo($_FILES["postimg"]["name"], PATHINFO_EXTENSION));
        $imgName = time() . "." . $imageFileType;
        $targetFile = $targetDir . $imgName;

        //Creates a directory specified by the targetDir variable that has the most vast permission, allowing files to be read and written to
        if(!file_exists($targetDir)){
            mkdir($targetDir, 0777);
        }

        //Gets the temp folder directory, gets the tmp file name, and combines them with a directory separator and sets it to the tempPath
        $tmpDir = sys_get_temp_dir();
        $tmpName = basename($_FILES['postimg']['tmp_name']);
        $tempPath = $tmpDir . DIRECTORY_SEPARATOR . $tmpName;
        
        //Ensures that the file type is an approved type: PNG, JPEG, JGP, GIF
        if ($imageFileType != "jpeg" && $imageFileType != "png" && $imageFileType != "jpg" && $imageFileType !="gif"){
            $err = "Image must be a JPG, JPEG, PNG, or GIF.";
        }
        
        //Moves the file from the temp directory, renames it, and places it in the target directory. If the file cannot be moved, it prints an error screen
        if (move_uploaded_file($tempPath, $targetFile)){
            // Check caption with contentFilter
            $caption = $_POST['caption'];

            try {
                $illegalPost = checkIllegalWords($_POST['caption'], $filterFile);
                if ($illegalPost !== null) {
                    echo "Your post contains prohibited content: $illegalPost. Please remove it and try again."; // Needs to be changed other than pop up
                    exit();
                }
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
                exit();
            }

            try{
                //Creates variables to be passed to the query
                $userID = $_SESSION['userID'];

                // Replace HTML special characters to prevent injection
                $caption = htmlspecialchars($caption, ENT_QUOTES, 'UTF-8');

                //Connects to the database and sets the error mode and the default fetch mode
                $db = getDatabaseConnection(); //Change to your database credentials
                $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
                $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                //Runs an insert query, adding the caption, posterid and image url to a row in the posts table
                $query = "INSERT INTO posts (caption, posterid, imgurl, lat, lon) VALUES ('$caption', '$userID','/src/PHP-Backend/$targetFile', '$lat', '$lon')";
                $stmt = $db->query($query);

                //Redirects to the feed page
                header('Location: /src/Pages/feed-page.php');

            } catch (PDOException $e) {
                //If the PDO connection fails, prints an error message to the screen.
                echo  "Connection failed: " . $e->getMessage();
                exit();
            }
        } else {
            //If the file cannot be moved, prints an error message to the screen.
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }
?>
