<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");

	session_name("tel_admin");
	session_start();
	$cn = db_connect();

	if(!isset($_SESSION['user_id']) || !isset($_SESSION['hash'])) {
		header("Location:/admin/index.php");
	} else {
		// SESSION値あり
		$sql = "select count(*) from admin where hash = '".$_SESSION['hash']."' and user_id = '".$_SESSION['user_id']."'";
		$result = query_exec($cn,$sql);
		$count = result_assoc($result);
		if($count['count'] == 0){
			header("Location:/admin/index.php");
		}
	}
	db_close ($cn);
?>