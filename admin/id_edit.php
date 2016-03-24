<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	require_once("./auth.php");
	require_once("./id_insert_check.php");
	require_once("./include.php");

	session_name("tel_admin");
	session_start();
	
	$cn = db_connect();

	//権限チェック
	admin_check($_SESSION['user_id'],$cn);

	//IDリスト取得
	$sql = "select id,authority,user_id,to_char(date_insert,'YYYY.MM.DD') as date_insert,to_char(date_update,'YYYY.MM.DD') as date_update from admin order by id";
	$result = query_exec($cn,$sql);
	$list = result_all($result);
	
	$Tpl->assign("list",$list);
	
	$Tpl->display('admin/id_edit.tpl.html');

	db_close ($cn);
?>