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
      if(file_exists($sessid) or file_exists($sessid . '.time')){
        header('Location: /login.php?message=Already%20logged%20in%20elsewhere');
      }else{
        $userfile = fopen($sessid, "w");
        $timefile = fopen($sessid . '.time', "w");
        fwrite($userfile, $email);
        fwrite($timefile, time()+3600);
        setcookie("PHPSESSID", $sessid, time()+3600);
        header('Location: /index.php');
      }
    } else {
      setcookie("PHPSESSID", authenticated_session($passwd), time()-7200);
      header('Location: /login.php?message=Login%20Failed');
    }
  }
  
  function logout($id) {
    $userfile = fopen($id, "r+");
    $timefile = fopen($id . '.time', "r+");
    delete($userfile);
    delete($timefile);
    setcookie("PHPSESSID", $id, time()-7200);
  }
  
  function is_authenticated($id) {
    if(file_exists($id) and file_exists($id . '.time')){
      $userfile = fopen($id, "r+");
      $timefile = fopen($id . '.time', "r+");
      if(fread($timefile, filesize($timefile)) < time()){
        delete($userfile);
        delete($timefile);
        return false;
      }else{
        $timefile = fopen($sessid . '.time', "w");
        fwrite($timefile, time()+3600);
        setcookie("PHPSESSID", $sessid, time()+3600);
        return fread($userfile, filesize($userfile));
      }
    }else{
      if(file_exists($id)){
        $userfile = fopen($id, "r+");
        delete($userfile);
      }
      if(file_exists($id . '.time')){
        $timefile = fopen($id . '.time', "r+");
        delete($timefile);
      }
      return false;
    }
  }
  
?>
