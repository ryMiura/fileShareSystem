<?php
session_start();

require_once("smarty_init.php");
require_once 'classes/Filelogic.php';

if(isset($_SESSION['us']) && $_SESSION['us'] != null){
  $pdo = connect();
  if(isset($_FILES['myf'])){
    $smarty->assign("disp","OK");
    //$_FILESに格納されている「myf」の情報を変数$fileに代入
    $file = $_FILES['myf'];
    $usid = $_SESSION['usid'];
    //ログインユーザーのファイルがなければ作成
    if(!file_exists ('./filedata/'.$usid)){
      mkdir('./filedata/'.$usid, 0777);
    }
    $filepath = './filedata/'.$usid.'/'.$file['name'];
    //同じファイルが存在していたら名称を変更する
    $new_name = FileLogic::file_cut($filepath);
    $filepath = './filedata/'.$usid.'/'.$new_name;
    //同名のファイルがあるかを調べる
    $filepath = unique_filename($filepath);
    //アップロードされたデータを「filedata/ファイル名」に移動している
    move_uploaded_file($file['tmp_name'],$filepath);
    $smarty->assign("file",$file);
    $smarty->assign("filename",pathinfo($filepath)['basename']);

    //画像タイプだったら画像も表示させる
    if(($file['type']=="image/jpeg")||($file['type']=="image/png")||($file['type']=="image/gif")){
      $smarty->assign("img",$filepath);
    }else{
      $smarty->assign("img","");
    }

    $fileData = [];

    $fileData[] = pathinfo($filepath)['basename'];
    $fileData[] = $file['size'];
    $fileData[] = date("Y/m/d");
    $fileData[] = $_SESSION['usid'];

    //ファイル情報をデータベースに登録
    $hasinputed = FileLogic::inputFile($fileData);
    if(!$hasinputed){
      echo '登録失敗';
    }

  }else{
    $smarty->assign("disp","");
  }
  $smarty->display("setup.tpl");
}else{
  session_destroy();
  header('Location:login.php');
}
