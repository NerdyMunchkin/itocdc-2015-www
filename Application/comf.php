<?php
include 'config.php';
include 'password.php';
include 'opendb.php';

echo("<span style=\"font-family: Courier New\">USERNAME | PASSWORD | HASHWORD<br>");

$query = $db->prepare("SELECT username, password FROM users");
$query->execute();
echo($query->rowCount() . " ROWS<br>");
if($query->rowCount() > 0){
  while($usersRow = $query->fetch()){
    $username = $usersRow[0];
    $password = $usersRow[1];
    echo($username . " | " . $password . " | ");
    if(strlen($password)<16){
      $password = password_hash($password, PASSWORD_DEFAULT);
      $querytwo = $db->prepare("UPDATE users SET password=:password WHERE username=:username");
      $querytwo->bindParam(":username",$username,strlen($username));
      $querytwo->bindParam(":password",$password,strlen($password));
      $querytwo->execute();
    }
    echo($password . "<br>");
  }
} else {
  echo "<h1>what</h1>";
}
echo("<h1>DONE</h1>");
include 'closedb.php';
