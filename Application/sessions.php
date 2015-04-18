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
    include 'closedb.php';
  }
  
  function logout($id) {
    delete($id);
    delete($id . '.time');
    setcookie("PHPSESSID", $id, time()-7200);
  }
  
  function is_authenticated($id) {
    if(file_exists($id) and file_exists($id . '.time')){
      $userfile = fopen($id, "r+");
      $timefile = fopen($id . '.time', "r+");
      if(fread($timefile, filesize($id . '.time')) < time()){
        unlink($id);
        unlink($id . '.time');
        return false;
      }else{
        $timefile = fopen($sessid . '.time', "w");
        fwrite($timefile, time()+3600);
        setcookie("PHPSESSID", $sessid, time()+3600);
        return fread($userfile, filesize($id));
      }
    }else{
      if(file_exists($id)){
        unlink($id);
      }
      if(file_exists($id . '.time')){
        unlink($id . '.time');
      }
      return false;
    }
  }
  
?>
