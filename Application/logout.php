<?php
// clear session
setcookie("PHPSESSID", authenticated_session($email), time()-7200);
setcookie("user", $email, time()-7200);
header('Location: /index.php');
?>
