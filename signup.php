<?php
session_start();

$err=$_SESSION['err'];
require_once("smarty_init.php");
require_once 'db_connect.php';

//登録情報で正規表現エラーがあったら
if(isset($err)){
  //エラーを表示する
  $smarty->assign("err",$err);
}else{
  $smarty->assign("err",'');
}

//県情報取り出し
$pdo = connect();
$ps = $pdo->query('SELECT * FROM ken');

$kendata = [];
if($ps->rowCount()>0){
  while($r=$ps->fetch()){
    $kendata[] = $r['name'];
  }
}

$smarty->assign("kens",$kendata);
$smarty->display("signup.tpl");
