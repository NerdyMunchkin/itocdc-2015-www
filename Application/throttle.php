<?php
function logRquest($type) {
  try{
    $insert = $db->prepare("INSERT requests (id, type, ip, time) VALUES (:id, ':type', ':ip', :time)");
    $insert->bindParam(":id", microtime(true));
    $insert->bindParam(":type", strlen($type));
    $insert->bindParam(":ip", $_SERVER['REMOTE_ADDR'], strlen($_SERVER['REMOTE_ADDR']));
    $insert->bindParam(":time", time());
    $insert->execute();
  } catch(Exception $e) {
    error_log("Error logging request: " . $e, 0);
  }
}

function checkRequests($type, $time){
  try{
    $query = $db->prepare("SELECT time FROM requests WHERE (type=:type AND ip=:ip AND time>:time)");
    $query->bindParam(":type", strlen($type));
    $query->bindParam(":ip", strlen($_SERVER['REMOTE_ADDR']));
    $query->bindParam(":time", time()-$time);
    $query->execute();
    return $query->rowCount();
  } catch(Exception $e) {
    error_log("Error checking requests: " . $e, 0);
  }
}
?>
