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
include 'throttle.php';

if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  header('Location: /login.php?message=Invalid%20input');
  exit();
} else if(checkRequests("login", 30) > 3) {
  header('Location: /login.php?message=' . urlencode("Too many attempts, just wait a few seconds"));
  exit();
}

logRequest("login");

authenticate($email, $password);

// close connection to the database
include 'closedb.php';

?>
