<?php
session_start();

require_once("smarty_init.php");
require_once 'classes/UserLogic.php';
require_once 'classes/MemberController.php';
//エラーメッセージはここに入れていく
$err=[];


//バリデーション
if(!$first_name=filter_input(INPUT_POST,'first_name')){
  $err['first_name']='氏名を記入してください';
}else if(!preg_match("/^[A-Za-z0-9ぁ-んァ-ン一-龥]{1,16}$/",$first_name)){
  $err['first_name']='氏名はで1文字以上、16文字以下ににしてください';
}
if(!$last_name=filter_input(INPUT_POST,'last_name')){
  $err['$last_name']='氏名を記入してください';
}else if(!preg_match("/^[A-Za-z0-9ぁ-んァ-ン一-龥]{1,16}$/",$last_name)){
  $err['$last_name']='氏名はで1文字以上、16文字以下ににしてください';
}
$password=filter_input(INPUT_POST,'password');
//正規表現
if(!preg_match("/^[A-Za-z0-9]{4,16}$/",$password)){
  $err['password']='パスワードは英数字で4文字以上、16文字以下ににしてください';
}
$email=filter_input(INPUT_POST,'email');
if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)) {
    $err['email']='アドレスの形式が不一致です';
}

$ken = filter_input(INPUT_POST,'ken');
$gender = filter_input(INPUT_POST,'gender');
//エラーがない時
if(count($err) === 0){

  $hasCreated = UserLogic::createPreUser($_POST);
  $userData   = UserLogic::get_preusers($email);

  if(!$hasCreated){
    $err['email']='既に登録ずみのアドレスです';
  }else{
    $memberController = MemberController::mail_to_premember($userData);
    $alert = "<script type='text/javascript'>alert('ご入力いただいたメールアドレスへ登録ご案内を送信しました。メール中のリンクをクリックして登録を完了してください');window.location.href = 'login.php';</script>";
    echo $alert;
  }
}
//エラーが存在するときはsignup.phpに戻す
if(count($err)>0){

  $_SESSION['err']=$err;
  header('Location: signup.php');
}
