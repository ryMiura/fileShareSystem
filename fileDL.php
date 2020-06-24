<?php
 function fileDL($id){
  require_once("smarty_init.php");
  require_once("db_init.php");
  require_once("file_download.php");
  require_once("Pager/Pager.php");

  $ps= $pdo->query("SELECT * FROM `filetable` WHERE `id`='$id'");

  if($ps->rowCount()>0){
    while($r=$ps->fetch()){
      $filename= "./filedata/".$r['name'];
      $alert = "<script type='text/javascript'>alert('ss！');</script>";
      echo $alert;
      //ファイル名を指定してダウンロード
      download($filename);
    }
  }
}


 ?>
