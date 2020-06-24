<?php
//セッションスタート
session_start();
//ファイル読み込み
require_once("smarty_init.php");
require_once 'db_connect.php';
require_once("file_download.php");
require_once("PEAR/Pager/Pager.php");
require_once 'classes/FileLogic.php';


$user=$_SESSION['us'];
//ログイン中のユーザーのidを$usidに代入
$usid=$_SESSION['usid'];

//ログインされている状態だったら処理開始
if(isset($_SESSION['us']) && $_SESSION['us'] != null){


 $pdo = connect();
 /***********************************
  * 削除ボタンが押されたときの処理
  ***********************************/
 if (isset($_POST['del'])) {
   //チェックされた項目がNULLではないときと配列であるとき
   if (isset($_POST['check']) && is_array($_POST['check'])) {
     //どのファイルNOを削除するのかを代入
      $check = $_POST["check"];
      //チェックされたファイルデータを全てdeleteする
      foreach ($check as $c) {
        try{
          $ps2  = $pdo->query("SELECT * FROM `filetable` WHERE `id`='$c'");
          while($r=$ps2->fetch()){
            if(!FileLogic::file_delete('./filedata/'.$usid.'/'.$r['name'])){
              echo "データ削除中にエラーが発生しました。";
            }
          }
        }catch(Exception $e){
          echo "データ削除中にエラーが発生しました。";
          exit();
        }
      }
      foreach ($check as $c) {
        try{
          $ps  = $pdo->query("DELETE FROM `filetable` WHERE `id`='$c'");
        }catch(Exception $e){
          echo "データ削除中(DB)にエラーが発生しました。";
          exit();
        }
      }

      $alert = "<script type='text/javascript'>alert('チェックされた項目を削除しました！');</script>";
      echo $alert;

   }else{
     //チェックが選択されていないとき
     $alert = "<script type='text/javascript'>alert('削除するファイルをチェックして下さい');</script>";
     echo $alert;
   }

 }
 //ユーザー名を表示
 $smarty->assign('usname', $_SESSION['us']);



 /**********************************
  * アップロードしたファイルリストを表示
  **********************************/
    $smarty->assign('panel', 'private');
  if(isset($_POST['p_or_s'])){
    $smarty->assign('panel', 'share');
  }

 try{

   $ps= $pdo->query("SELECT * FROM `filetable` WHERE ope='$usid'");


   $smarty->assign('change', 'non');
   $smarty->assign('change_private_sort', 'non');

   //昇順降順のセレクトボックスが選択されたら
   if (isset($_POST['order'])) {
     if($_POST['order']==="asc"){
       $ps= $pdo->query("SELECT * FROM `filetable` WHERE ope='$usid' ORDER BY size ASC");
       $smarty->assign('change_private_sort', 'asc');
     }else if($_POST['order']==="desc"){
       $ps= $pdo->query("SELECT * FROM `filetable` WHERE ope='$usid' ORDER BY size DESC");
       $smarty->assign('change_private_sort', 'desc');
     }

   }
   //日付の昇順、降順が選択されたら
   if (isset($_POST['dateorder'])) {
     if($_POST['dateorder']==="asc"){
       $ps= $pdo->query("SELECT * FROM `filetable` WHERE ope='$usid' ORDER BY ymd ASC");
       $smarty->assign('change', 'asc');
     }else if($_POST['dateorder']==="desc"){
       $ps= $pdo->query("SELECT * FROM `filetable` WHERE ope='$usid' ORDER BY ymd DESC");
       $smarty->assign('change', 'desc');
     }
   }

   //検索ボタンが押されて、検索ワードが入力されていたら
   if(isset($_POST['search'])){
     $like= $_POST['searchText'];

     $ps= $pdo->query("SELECT * FROM `filetable` WHERE ope='$usid' AND name LIKE '%$like%'");
   }
   //クリアボタン
   if(isset($_POST['clear'])){

     $ps= $pdo->query("SELECT * FROM `filetable` WHERE ope='$usid'");
   }
   $personaldata=[];


   if($ps->rowCount()>0){
     while($r=$ps->fetch()){
       $personaldata[]=array(
         $r['name'],$r['ymd'],$r['size'],$r['id']
       );
     }

     $smarty->assign('personaldata', $personaldata);
     $smarty->assign('filelist', "OK");
     $cnt = count($personaldata);$options = array(
       //トータルアイテム数
       "totalItems" => $cnt,
       //ページリンク数
       //"delta" => 5,
       //1ページに表示させるアイテム数
       "perPage" => 7,
       "itemData"=>$personaldata
     );
     $pager =& Pager::factory($options);
     $navi = $pager -> getLinks();
     $smarty->assign('navi', $navi["all"]);

     $currentPageID = $pager -> getCurrentPageID();
     $index = ($currentPageID - 1) * 7 + 1;

     $smarty->assign("index",$index);
     $smarty->assign("cnt",$cnt);
     $smarty->assign("data",$personaldata);
     $smarty->assign("page",$currentPageID);

   }else{
     $smarty->assign('filelist', "");
   }

    /************************************
     * ファイル共有タブの中の処理
     ************************************/
   $personaldata_share=[];
   $psshare= $pdo->query("SELECT * FROM `filetable`");

   $smarty->assign('s_size_sort', 'non');
   $smarty->assign('s_date_sort', 'non');

   //昇順降順のセレクトボックスが選択されたら
   if (isset($_POST['s_order'])) {
     if($_POST['s_order']==="asc"){
       $psshare= $pdo->query("SELECT * FROM `filetable` ORDER BY size ASC");
       $smarty->assign('s_size_sort', 'asc');
     }else if($_POST['s_order']==="desc"){
       $psshare= $pdo->query("SELECT * FROM `filetable` ORDER BY size DESC");
       $smarty->assign('s_size_sort', 'desc');
     }
   }
   //日付の昇順、降順が選択されたら
   if (isset($_POST['s_dateorder'])) {
     if($_POST['s_dateorder']==="asc"){
       $psshare= $pdo->query("SELECT * FROM `filetable`  ORDER BY ymd ASC");
       $smarty->assign('s_date_sort', 'asc');
     }else if($_POST['s_dateorder']==="desc"){
       $psshare= $pdo->query("SELECT * FROM `filetable`  ORDER BY ymd DESC");
       $smarty->assign('s_date_sort', 'desc');
     }
   }
   //リストが1件でもあったら表示
   if($psshare->rowCount()>0){
     while($r=$psshare->fetch()){

       $personaldata_share[]=array(
         $r['name'],$r['ymd'],$r['size'],$r['id'],$r['ope']
       );
     }

     $cnt2 = count($personaldata_share);$options2 = array(
       //トータルアイテム数
       "totalItems" => $cnt2,
       //ページリンク数
       //"delta" => 5,
       //1ページに表示させるアイテム数
       "perPage" => 7,
       "itemData"=>$personaldata_share
     );
     $smarty->assign('filelist2', "OK");
     $pager2 =& Pager::factory($options2);
     $navi2 = $pager2 -> getLinks();
     $smarty->assign('navi2', $navi2["all"]);

     $currentPageID2 = $pager2 -> getCurrentPageID();
     $index2 = ($currentPageID2 - 1) * 7 + 1;
     $smarty->assign("usid",$usid);
     $smarty->assign("index2",$index2);
     $smarty->assign("cnt2",$cnt2);
     $smarty->assign("data2",$personaldata_share);
     $smarty->assign("page2",$currentPageID2);

   }else{
     $smarty->assign('filelist2', "");
   }

 }catch(Exception $e){
   echo "エラーが発生しました。";
 }

//ログインされていない状態だったら
}else{

 session_destroy();
 header('Location:login.php');
}
$smarty->display("home.tpl");
