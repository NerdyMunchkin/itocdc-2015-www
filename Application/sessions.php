<?php
  function authenticated_session($passwd) {
    return hash("sha256", hash("sha256", $passwd));
  }
  
  function authenticate($email, $passwd) {
    include 'opendb.php';
    include 'password.php';
    $query = $db->prepare('SELECT * FROM users WHERE email=:email');
    $query->execute(array(':email' => $email));
    $result = $query->fetch(PDO::FETCH_ASSOC);
    $login = password_verify($passwd, $result['password']);
    if($login){
      $sessid = authenticated_session($passwd);
      if(file_exists($sessid) or file_exists($sessid . '.time') or file_exists($sessid . '.ip')){
        header('Location: /login.php?message=Already%20logged%20in%20elsewhere');
      }else{
        $userfile = fopen($sessid, "w");
        $timefile = fopen($sessid . '.time', "w");
        $clientfile = fopen($sessid . '.ip', "w");
        fwrite($userfile, $email);
        fwrite($timefile, time()+3600);
        fwrite($clientfile, $_SERVER['REMOTE_ADDR']);
        setcookie("PHPSESSID", $sessid, time()+3600, true);
        header('Location: /index.php');
      }
    } else {
      setcookie("PHPSESSID", authenticated_session($passwd), time()-7200, true);
      header('Location: /login.php?message=Login%20Failed');
    }
    include 'closedb.php';
  }
  
  function logout($id) {
    unlink($id);
    unlink($id . '.time');
    unlink($id . '.ip');
    setcookie("PHPSESSID", $id, time()-7200, true);
  }
  
  function get_email($id){
    if(file_exists($id)){
      $userfile = fopen($id, "r+");
      return fread($userfile, filesize($id));
    }else{
      return null;
    }
  }
  
  function is_authenticated($id) {
    if(file_exists($id) and file_exists($id . '.time') and file_exists($id . '.ip')){
      $userfile = fopen($id, "r+");
      $timefile = fopen($id . '.time', "r+");
      $sessionfile = fopen($id . '.ip', "r+");
      if(fread($timefile, filesize($id . '.time')) < time()){
        unlink($id);
        unlink($id . '.time');
        unlink($id . '.ip');
        return false;
      } else if(fread($sessionfile, filesize($id . '.ip')) != $_SERVER['REMOTE_ADDR']){
        unlink($id);
        unlink($id . '.time');
        unlink($id . '.ip');
        return false;
      } else{
        $timefile = fopen($id . '.time', "w");
        fwrite($timefile, time()+3600);
        setcookie("PHPSESSID", $id, time()+3600, true);
        return fread($userfile, filesize($id));
      }
    }else{
      if(file_exists($id)){
        unlink($id);
      }
      if(file_exists($id . '.time')){
        unlink($id . '.time');
      }
      if(file_exists($id . '.ip')){
        unlink($id . '.ip');
      }
      return false;
    }
  }
  
?>
