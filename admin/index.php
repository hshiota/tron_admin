<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	session_name("tel_admin");
	session_start();
	session_unset();

	//ログイン画面表示
	$Tpl->display('admin/index.tpl.html');
?>