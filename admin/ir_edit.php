<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	require_once("./auth.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lang.php");
	require_once("./include.php");

	session_cache_limiter('private, must-revalidate');
	session_name("tel_admin");
	session_start();
	
	$cn = db_connect();

	//送信先
	define(LIST_PAGE,"ir_edit.php");
	$Tpl->assign("action","ir_edit.php");
	//現在のニュース選択
	$select = "ir";
	$Tpl->assign("select",$select);
	//文字コード
	$Tpl->assign("charset",CHARSET_SELECT);
	
	//編集可能チェック
	$flg = check_edit($_SESSION['code'],$select);
	if($flg == false){
		header("Location:./top.php");
	}
	
	require_once("./article_edit.php");
	
?>