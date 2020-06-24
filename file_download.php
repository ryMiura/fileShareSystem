<?php
function download($pPath, $pMimeType = null)
{
    //-- ファイルが読めない時はエラー
    if (!is_readable($pPath)) { die($pPath); }


    //-- 適切なMIMEタイプが得られない時は、未知のファイルを示すapplication/octet-streamとする
    if (!preg_match('/\A\S+?\/\S+/', $mimeType)) {
        $mimeType = 'application/octet-stream';
    }

   //-- Content-Type
   header('Content-Type:  . $mimeType;charset=UTF-8');

   //-- ウェブブラウザが独自にMIMEタイプを判断する処理を抑止する
   header('X-Content-Type-Options: nosniff');

   //-- ダウンロードファイルのサイズ
   header('Content-Length: ' . filesize($pPath));

   //-- ダウンロード時のファイル名
   header('Content-Disposition: attachment; filename="' . basename($pPath) . '"');

   //-- keep-aliveを無効にする
   header('Connection: close');

   //-- readfile()の前に出力バッファリングを無効化する
   while (ob_get_level()) { ob_end_clean(); }

   //-- 出力
   readfile($pPath);

   exit;
}
