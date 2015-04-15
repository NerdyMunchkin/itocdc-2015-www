<?php
  function authenticated_session($passwd) {
    return hash("sha256", hash("sha256", passwd));
  }
  
  function authenticate($email, $passwd) {
    $query = $db->prepare('SELECT * FROM users WHERE email=:email AND password=:password');
    $query->bindParam(':email', $email, PDO::PARAM_STR, strlen($email));
    $query->bindParam(':password', $password, PDO::PARAM_STR, strlen($password));
    $query->execute();
    
    $login = $query->rowCount();
    if($login){
      $sessid = authenticated_session($passwd);
      if(file_exists($sessid)){
        header('Location: /login.php?message=Already%20logged%20in%20elsewhere');
      }else{
        $userfile = fopen($sessid, "w");
        fwrite($userfile, $email);
        setcookie("PHPSESSID", $sessid, time()+3600);
        header('Location: /index.php');
      }
    } else {
      setcookie("PHPSESSID", authenticated_session($passwd), time()-7200);
      header('Location: /login.php?message=Login%20Failed');
    }
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
