<?php

require_once 'db_connect.php';
require_once 'classes/UserLogic.php';
  //リンクパスとメールアドレスを$_GETしていたら
  if((isset($_GET['link_pass'])) && (isset($_GET['email']))){
    $email = $_GET['email'];
    //データベース pre→primaryに移動する
    $pdo  = connect();
    $userData = [];
    try{
      //メールアドレスのemailと一致するデータをDBから取り出す
      $ps = $pdo->query("SELECT * FROM pre_users WHERE email = '$email'");
      //データが存在したら
      if($ps->rowCount()>0){
        while($r=$ps->fetch()){
          $userData = $r;
        }
        //リンクパスがDBの情報と一致するとき登録作業を行う
        if($userData['link_pass'] == $_GET['link_pass']){
          //削除
          $psd = $pdo->query("DELETE FROM pre_users WHERE email = '$email'");

          //本会員登録登録メソッドを呼び出す
          $hasCreated = UserLogic::primary_usercreate($userData);
          if(!$hasCreated){
            $message = '登録失敗';
          }else{
            $message = '登録が完了しました。トップページよりログインしてください';
          }
        }

      }else{
        //データは存在しないということなので、登録失敗にする
        $message = "メールアドレスのデータはありません：登録失敗";
      }
    }catch(Exception $e){
      echo $e->getMessage();
    }
  //＄＿GETにリンクパスかメールアドレスがない場合
  }else{
    $message = 'このURLは無効です';
  }
 ?>


<!DOCTYPE html>
<html lang="ja" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <title></title>

  </head>
  <body>
    <hr>
    <div class="top mt-5">
      <p class = "text-center"><?php echo $message ?></p>
      <p class = "text-center"><a href="login.php">トップページ</a></p>
    </div>
    <hr>


  </body>
</html>
