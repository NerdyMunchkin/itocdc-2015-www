<?php
include 'config.php';
// opens the database connection
try{
  $db = new PDO("mysql:host=$DATABASE_IP;dbname=$DATABASE_NAME", $DATABASE_USERNAME, $DATABASE_PASSWORD);
} catch(Exception $e){
  echo "Error opening DB: $e";
}
?>
