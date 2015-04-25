<?php
function logRequest($type) {
  try{
    include 'opendb.php';
    $insert = $db->prepare("INSERT requests (id, type, ip, time) VALUES (:id, ':type', ':ip', :time)");
    $insert->bindParam(":id", microtime(true));
    $insert->bindParam(":type", strlen($type));
    $insert->bindParam(":ip", $_SERVER['REMOTE_ADDR'], strlen($_SERVER['REMOTE_ADDR']));
    $insert->bindParam(":time", time());
    $insert->execute();
    include 'closedb.php';
  } catch(Exception $e) {
    error_log("Error logging request: " . $e, 0);
  }
}

function checkRequests($type, $time){
  try{
    include 'opendb.php';
    $query = $db->prepare("SELECT time FROM requests WHERE (type=:type AND ip=:ip AND time>:time)");
    $query->bindParam(":type", strlen($type));
    $query->bindParam(":ip", $_SERVER['REMOTE_ADDR'], strlen($_SERVER['REMOTE_ADDR']));
    $pasttime = time()-$time;
    $query->bindParam(":time", $pasttime, PDO::PARAM_INT);
    $query->execute();
    return $query->rowCount();
    include 'closedb.php';
  } catch(Exception $e) {
    error_log("Error checking requests: " . $e, 0);
  }
}
?>
