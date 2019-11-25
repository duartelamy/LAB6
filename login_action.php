<?php
include 'db.php';
session_start();
$db = dbconnect($hostname,$db_name,$db_user,$db_passwd);
$Error = null;
if($db && !empty($_REQUEST))
  $Error = statement($db);

mysql_close($db);

if($Error == 1) {
  $_SESSION['Error'] = true;
  header("Location:login.php");
} else {
  header("Location: index.php");
}

function statement($db) {

  $Email = $_REQUEST['Email'];
  $Password = $_REQUEST['Pwd'];
  $RememberMe = $_REQUEST['rememberMe'];

  //Check Email
  $tuple = checkEmail($db,$Email);
  if($tuple == false)
    return 1;
  
  //Check Passwords
  $pwdHashes = substr(md5($_REQUEST['Pwd']), 0,32);
  $PasswordState = checkPasswords($tuple[0]['password_digest'],$pwdHashes);
  if($PasswordState == false)
    return 1;

  $_SESSION['Name'] = $tuple[0]['name'];
  $_SESSION['Id'] = $tuple[0]['id'];
  $Uid = $_SESSION['Id'];

  if($RememberMe == true) {
    $Value = substr(md5(time()), 0,32);
    $MaxTime = time() + (3600 * 24 * 30);
    setcookie("rememberMe",$Value,$MaxTime);

    $Cookie = "UPDATE users
               SET users.remember_digest = '$Value'
               WHERE users.id = $Uid";          
    if(!($result = @ mysql_query($Cookie,$db)))
      showerror();
  }

  return 0;
}
// check if email exists
function checkEmail($db,$Email) {

  $query = "SELECT email,password_digest,id,name FROM users WHERE email='$Email'";

  if(!($result = @ mysql_query($query,$db))) 
    showerror();

  // vai buscar o resultado da query
  $nrows  = mysql_num_rows($result);
  if($nrows > 0) {
    $tuple[0] = mysql_fetch_array($result,MYSQL_ASSOC);
    return $tuple;
  }
  return false;

}

function checkPasswords($Pwd,$CfmPwd) {
  if($Pwd == $CfmPwd)
    return true;
  return false;
}

function redirect($location,$Email,$Username,$Error) {

   header("Location: $location?Error=$Error&Email=$Email&Username=$Username");

}
?>