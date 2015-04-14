<?php

// session utils
include 'sessions.php';

// get POST information from login form
$email=$_POST["email"];
$password=$_POST["password"];

// open connection to the database
include 'config.php';
include 'opendb.php';

$query = $db->prepare('SELECT * FROM users WHERE email=:email AND password=:password');
$query->bindParam(':email', $email, PDO::PARAM_STR, strlen($email));
$query->bindParam(':password', $password, PDO::PARAM_STR, strlen($password));
$query->execute();

// authenticate user
$login = $query->rowCount();

if($login){
  // set an active cookie for this username
  setcookie("PHPSESSID", authenticated_session($email), time()+3600);
  setcookie("user", $email, time()+3600);
  header('Location: /index.php');
} else {
  // logout
  setcookie("PHPSESSID", authenticated_session($email), time()-7200);
  setcookie("user", $email, time()-7200);
  header('Location: /login.php?message=Login%20Failed');
}

// close connection to the database
include 'closedb.php';

?>
