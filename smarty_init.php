<?php
require_once("smarty/libs/Smarty.class.php");
$smarty = new Smarty();

$smarty->template_dir = './smarty/templates/';
$smarty->compile_dir  = './smarty/templates_c/';
$smarty->config_dir   = './smarty/configs/';
$smarty->cache_dir    = './smarty/cache/';
