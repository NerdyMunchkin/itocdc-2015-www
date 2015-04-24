<?php

include 'config.php';
include 'sessions.php';

// open connection to the database
include 'opendb.php';

function generateShortName($file) {
    return hash('sha256', fread(fopen($file, "r+"), filesize($file)));
}


if ($_FILES["video"]["error"] == UPLOAD_ERR_OK) {
    if(isset($_POST["video"]) || isset($_POST["title"]) || isset($_POST["description"])){
        if($_FILES["video"]["size"] < 100000000){
            $email = is_authenticated($_COOKIE["PHPSESSID"]);
            if($email){
                // check disk quota
                $query = $db->prepare('SELECT id, datause FROM users WHERE email=:email');
                $query->bindParam(':email', $email, strlen($email));
                $query->execute();
                $userRow = $query->fetch();
                $userID = $userRow[0];
                $useddisk = $userRow[1];
                
                if($useddisk < 1000000000){
                    // get filename
                    $filename = $_FILES["video"]["name"];
                  
                    // generate unique shortname for upload
                    $shortname = generateShortName($_FILES["video"]["tmp_name"]);
                    $extension = pathinfo($_FILES["video"]["name"], PATHINFO_EXTENSION);
                    if(file_exists($uploadDir . "/" . $shortname . "." .$extension)){
                      header("Location: /post.php?message=" . urlencode("Video already posted."));
                      exit();
                    }
                  
                    // check file type
                    if(in_array($_FILES["video"]["type"], $validMedia) != 1 || in_array($extension, $validMediaExtensions) != 1) {
                      header("Location: /post.php?message=" . urlencode("File format not supported."));
                      exit();
                    }
                    
                    // move file to upload directory
                    move_uploaded_file($_FILES["video"]["tmp_name"], $uploadDir . "/" . $shortname . "." .$extension);
                  
                    // check upload success
                    if(!file_exists($uploadDir . "/" . $shortname . "." .$extension)){
                      header("Location: /post.php?message=" . urlencode("Upload failed."));
                      exit();
                    }

                    // save input fields
                    $title = filter_var($_POST["title"], FILTER_SANITIZE_STRING);
                    $description = filter_var($_POST["description"], FILTER_SANITIZE_STRING);
                    try {
                      $query = $db->prepare('SELECT id FROM users WHERE email=:email');
                      $query->bindParam(':email', $email, strlen($email));
                      $query->execute();
                      $userRow = $query->fetch();
                      $userID = $userRow[0];
                
                      // insert video into clips table
                      //$insertResult = mysql_query("INSERT INTO clips (host, shortname, title, description, user, extension) VALUES ('$APPLICATION_HOSTNAME', '$shortname', '$title', '$description', '$userID', '$extension')");
                      $query = $db->prepare("INSERT INTO clips (host, shortname, title, description, user, extension) VALUES (:hostname, :shortname, :title, :description, :userID, :extension)");
                      $query->bindParam(':hostname', $APPLICATION_HOSTNAME, strlen($APPLICATION_HOSTNAME));
                      $query->bindParam(':shortname', $shortname, strlen($shortname));
                      $query->bindParam(':title', $title, strlen($shortname));
                      $query->bindParam(':description', $description, strlen($description));
                      $query->bindParam(':userID', $userID, strlen($userID));
                      $query->bindParam(':extension', $extension, strlen($extension));
                      $query->execute();
                      //TODO: add SQL error handling
                      header("Location: /view.php?video=" . $shortname);
                      exit();
                    } catch (Exception $e) {
                      header("Location: /post.php?message=" . urlencode("Error: " . $e));
                      exit();
                    }
                } else{
                    header("Location: /post.php?message=" . urlencode("You have used all your storage."));
                    exit();
                }
            } else {
                header("Location: /post.php?message=" . urlencode("Unauthenticated user."));
                exit();
            }
        } else{
            header("Location: /post.php?message=" . urlencode("File too big."));
            exit();
        }
  } else {
    header("Location: /post.php?message=" . urlencode("Upload failed."));
    exit();
  }
} else {
  // file upload failed
  header("Location: /post.php?message=" . urlencode("No video imported."));
  exit();
}

include 'closedb.php'
?>

