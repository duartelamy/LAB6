<?php

include 'db.php';
require('libs/Smarty.class.php');

session_start();
$smarty = buildSmarty();

//Array for menus
$SA_Menu = array (
  "Home0" => "Home",
  "Register2" => "Register",
  "href0" => "index.php",
  "href2" => "register.php"
);

$State = checkError();

smartyAssign($SA_Menu,$State,$smarty);
smartyDisplay($smarty);


function checkError() {

  if(isset($_SESSION['Error'])) {
    session_destroy();
    return "Os dados estão incorretos";
  } return false;
}

function buildSmarty(){

  $smarty = new Smarty();

  $smarty->template_dir = 'templates';
  $smarty->compile_dir = 'templates_c';
  $smarty->cache_dir = 'cache';
  $smarty->config_dir = 'configs';
  return $smarty;

}

function smartyAssign($SA_Menu,$State,$smarty) {

  foreach ($SA_Menu as $key => $value) {
    $smarty->assign($key,$value);
  }
  if($State != false) {
    $smarty->assign('isError',1);
    $smarty->assign('Error',$State);
  } else {
    $smarty->assign('isError',0);
    $smarty->assign('Error',"");
  }
}

function smartyDisplay($smarty) {
  // Mostra a tabela
  $smarty->display('templates/login_template.tpl');
}
?>