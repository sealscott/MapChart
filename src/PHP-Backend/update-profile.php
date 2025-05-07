<?php
    session_start();
    ob_start();
    require_once "../PHP-Backend/functions.php";

    $err = ""; // Initialize a variable to store error messages

    if (isset($_FILES["profpic"]) && $_FILES["profpic"]["error"] === UPLOAD_ERR_OK) {

        $targetDir = "Profile-Pics/";
        $imageFileType = strtolower(pathinfo($_FILES["profpic"]["name"], PATHINFO_EXTENSION));
        $allowedTypes = ["jpeg", "png", "jpg", "gif"];
        $maxFileSize = 2 * 1024 * 1024; // 2 MB

        // Validate file type
        if (!in_array($imageFileType, $allowedTypes)) {
            $err = "Error: Only JPG, JPEG, PNG, and GIF files are allowed.";
        }

        // Validate file size
        if ($_FILES["profpic"]["size"] > $maxFileSize) {
            $err = "Error: File size exceeds the 2MB limit.";
        }

        if (empty($err)) {
            // Generate unique file name
            $imgName = time() . "_" . uniqid() . "." . $imageFileType;
            $targetFile = $targetDir . $imgName;

            // Create target directory if it doesn't exist
            if (!file_exists($targetDir)) {
                if (!mkdir($targetDir, 0777, true)) {
                    $err = "Error: Failed to create directory.";
                }
            }

            // Move uploaded file to target directory
            if (!move_uploaded_file($_FILES["profpic"]["tmp_name"], $targetFile)) {
                $err = "Error: Failed to upload file.";
            }
        }
    } elseif (isset($_FILES["profpic"]) && $_FILES["profpic"]["error"] !== UPLOAD_ERR_NO_FILE) {
        // Handle other file upload errors
        $err = "Error: File upload failed with error code " . $_FILES["profpic"]["error"] . ".";
    }

    if (!empty($err)) {
        // Echo the error, display a message, and exit
        echo "<p class='error'>$err</p>";
        echo "<p>Please go back and try again.</p>";
        exit();
    }

    try {
        // Create a new PDO connection and set attributes
        $db = getDatabaseConnection();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Get sanitized user inputs
        $userID = $_SESSION['userID'];
        $bio = htmlspecialchars($_POST['bio'], ENT_QUOTES, 'UTF-8');
        $disName = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');

        // Update user information
        if (isset($targetFile)) {
            $query = "UPDATE users SET bio = :bio, disName = :disName, profpicurl = :profpicurl WHERE uid = :userID";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':bio' => $bio,
                ':disName' => $disName,
                ':profpicurl' => "/src/PHP-Backend/$targetFile",
                ':userID' => $userID
            ]);
        } else {
            $query = "UPDATE users SET bio = :bio, disName = :disName WHERE uid = :userID";
            $stmt = $db->prepare($query);
            $stmt->execute([
                ':bio' => $bio,
                ':disName' => $disName,
                ':userID' => $userID
            ]);
        }

    } catch (PDOException $e) {
        echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</p>";
        exit();
    }

    // Redirect to the profile page after updating
    header('Location: /src/Pages/profile-page.php');
    exit();
?>
