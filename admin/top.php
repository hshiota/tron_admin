<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	require_once("./auth.php");
	require_once("./include.php");

	session_name("tel_admin");
	session_start();
	
	//権限別のメニュー表示
	$Tpl->assign("authority",$_SESSION['authority']);
	switch($_SERVER['SERVER_NAME']){
		case'vhost02.intra.jama.co.jp':
		case'temp-test.tel.com':
		case'temp-origin.tel.com':
		case'test.tel.com':
		case'origin.tel.com':
			$Tpl->assign("domain","com");
			break;
		case'vhost03.intra.jama.co.jp':
		case'temp-test.tel.co.jp':
		case'temp-origin.tel.co.jp':
		case'test.tel.co.jp':
		case'origin.tel.co.jp':
			$Tpl->assign("domain","cojp");
			break;
	}
	$Tpl->display('admin/top.tpl.html');
?>