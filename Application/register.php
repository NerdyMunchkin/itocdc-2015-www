<?php

include 'headers.php';
include 'sessions.php';
include 'config.php';

// get POST information from login form
$email=$_POST["email"];
$username=$_POST["username"];
$password=$_POST["password"];

// open connection to the database
include 'opendb.php';

try{
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        header('Location: /registration.php?message=' . urlencode('Email provided was invalid!'));
        exit();
    }else if(!ctype_alnum($username)){
        header('Location: /registration.php?message=' . urlencode('Username may only contain alphanumeric characters!'));
        exit();
    }

    $insert = $db->prepare("INSERT INTO users (email, username, password) VALUES ('$email', '$username', '$password')");
    $insert->execute();
	
    // register user
    if ($insert->rowCount()) {
    authenticate($email, $password);
    } else {
        header('Location: /registration.php?message=' . urlencode(mysql_error($conn)));
    } 
} catch(Exception $e) {
    header("Location: /registration.php?message=" . urlencode("Error: " . $e));
}

// close connection to the database
include 'closedb.php';

?>
