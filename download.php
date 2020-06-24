<?php

/************************************
 * ファイルのダウンロードに関するファイル
 ************************************/
require_once 'db_connect.php';
require_once("file_download.php");

session_start();

//きちんとログインしているであれば処理開始
if(isset($_SESSION['us']) && ($_SESSION['us'] != null)){
  //connect()の戻り値を代入
  $pdo = connect();
  //ダウンロードするファイルが選択されたら
  if(isset($_POST['dlno'])){
    //$dlnoにPOSTされた値を代入
    $dlno=$_POST['dlno'];
    $usid = $_SESSION['usid'];
    //データベースからファイル名を抽出してパスを指定する
    try{
      $ps= $pdo->query("SELECT * FROM `filetable` WHERE `id`='$dlno'");
      if($ps->rowCount()>0){
        while($r=$ps->fetch()){

          $filename= "./filedata/".$usid.'/'.$r['name'];
          //ファイル名を指定してダウンロード
          download($filename);
        }
      }
    }catch(Exception $e){
      echo "ダウンロードでエラーが発生しました。";
    }

  }else{
    //ファイルは選択されていないのでhome画面へ戻る
    header('Location:home.php');
  }

}else{
  //ログインされていないのでセッションを終了してログイン画面へ
  session_destroy();
  header('Location:login.php');
}
