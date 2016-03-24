<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");

	session_cache_limiter('private, must-revalidate');
	session_name("tel_admin");
	session_start();
	
	/*----------ヘッダーインクルード----------*/
	$header_include = file_get_contents($Tpl->template_dir."admin/header.tpl.html");
	$Tpl->assign("header",$header_include);
	
	/*----------フッターインクルード----------*/
	$footer_include = file_get_contents($Tpl->template_dir."admin/footer.tpl.html");
	$Tpl->assign("footer",$footer_include);

	$Tpl->assign("user_id",$_SESSION['user_id']);

?>