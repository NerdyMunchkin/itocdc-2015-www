<?php
include 'config.php';
include 'password.php';
include 'opendb.php';

echo("<span style=\"font-family: Courier New\">\")USERNAME | PASSWORD | HASHWORD");

$query = $db->prepare("SELECT username, password FROM users");
$query->execute();
echo($query->rowCount());
if($query->rowCount() > 0){
  while($usersRow = $query->fetch()){
    $username = $usersRow[0];
    $password = $usersRow[1];
    echo($username . " | " . $password . " | ");
    $password = password_hash($password);
    $querytwo = $db->prepare("UPDATE users SET password=:password WHERE username=:username");
    $querytwo->bindParam(":username",$username,strlen($username));
    $querytwo->bindParam(":password",$password,strlen($password));
    $querytwo->execute();
    echo($password . "<br>");
  }
} else {
  echo "<h1>No users found! :(</h1>";
}
