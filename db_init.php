<?php
//****************************
//データーベース接続
//****************************

//接続のための情報ファイルを読み込む
require_once("data/db_info.php");
//PHPエラーの表示・非表示を設定します。
//「1（true）」を設定するとエラー表示、「0（false）」は非表示
ini_set('display_errors',true);
try{
  $host=DB_HOST;
  $db  =DB_NAME;
  $user=DB_USER;
  $pass=DB_PASS;

  $dsn = "mysql:host=$host;dbname=$db;charaset=utf8mb4";
  $pdo = new PDO($dsn,$user,$pass,[
    //エラーのモード
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    //フェッチモードの設定　配列をキーとバリューで必ず返す
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);

}catch (Exception $e){
  //エラーの場合はエラーメッセージを表示する
  echo '接続失敗です！'.$e->getMessage();
  exit();
}
