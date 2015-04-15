<?php
  function authenticated_session($passwd) {
    return hash("sha256", hash("sha256", passwd));
  }
?>
