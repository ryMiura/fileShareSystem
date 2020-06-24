<?php

/******************************
 * データベースに接続するファイル
 *****************************/
require_once 'data/env.php';
ini_set('display_errors',true);
function connect(){

  $host = _DB_HOST;
  $db   = _DB_NAME;
  $user = _DB_USER;
  $pass = _DB_PASS;

  try{
    $dsn = "mysql:host=$host;dbname=$db;charaset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
      //エラーのモード
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      //フェッチモードの設定　配列をキーとバリューで必ず返す
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    return $pdo;
  }catch (PODException $e){
    //エラーの場合はエラーメッセージを表示する
    echo '接続失敗です！'.$e->getMessage();
    exit();
    return false;
  }
}
