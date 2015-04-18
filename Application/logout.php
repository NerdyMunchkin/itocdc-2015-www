<?php
include 'sessions.php';
// clear session
logout($_COOKIE["PHPSESSID"]));
header('Location: /index.php');
?>
