<?php
  function authenticated_session($passwd) {
    return hash("sha256", hash("sha256", passwd));
  }
  
  function is_authenticated($id) {
    if(file_exists($id)){
      $userfile = fopen($id, "r");
      return fread($userfile, filesize($userfile));
    }else{
      return false;
    }
  }
  
?>
