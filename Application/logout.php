<?php
// clear session
if (isset($_COOKIE['PHPSESSID'])) {
unset($_COOKIE['PHPSESSID']);
}
header('Location: /index.php');
?>
