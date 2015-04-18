<?php

// session utils
include 'sessions.php';

// get POST information from login form
$email=$_POST["email"];
$password=$_POST["password"];

// open connection to the database
include 'config.php';
include 'opendb.php';
include 'password.php';

$hashPass = password_hash($password, PASSWORD_DEFAULT);

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  //header('Location: /login.php?message=Invalid%20input');
  exit();
}

authenticate($email, $hashPass);

// close connection to the database
include 'closedb.php';

?>
