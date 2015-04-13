<?php
// clear session
setcookie("PHPSESSID", "", time()-7200);
header('Location: /index.php');
?>
