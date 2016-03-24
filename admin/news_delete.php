<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	require_once("./auth.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lang.php");
	require_once("./upload.php");
	require_once("./insert_check.php");
	require_once("./create_html.php");
	require_once("./create_list.php");
	require_once("./create_xml.php");
	require_once("./create_rss.php");
	require_once("./restructure_html.php");
	require_once("./id_check.php");
	require_once("./view_confirm.php");
	require_once("./include.php");

	session_cache_limiter('private, must-revalidate');
	session_name("tel_admin");
	session_start();
	
	$cn = db_connect();

	if($_SESSION['code'] != ""){
		$lang = $_SESSION['code'];
	}else {
		header("Location:./top.php");
	}
	
	//送信先
	$Tpl->assign("action","news_delete.php");
	//削除前のページ
	$Tpl->assign("back","news_update.php");
	//リストページの指定
	define(LIST_PAGE,"news_edit.php");
	$Tpl->assign("list_page","news_edit.php");
	//現在のモード
	$Tpl->assign("mode","delete");
	//現在のニュース選択
	$select = "news";
	$Tpl->assign("select",$select);
	//文字コード
	$Tpl->assign("charset",CHARSET_SELECT);
	
	//編集可能チェック
	$flg = check_edit($lang,$select);
	if($flg == false){
		header("Location:./top.php");
	}
	
	require_once("./article_delete.php");

?>