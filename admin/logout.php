<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");

	session_name("tes_admin");
	session_start();
	$cn = db_connect();

	//ログアウト
	$sql = "update admin set hash='' where hash='".$_SESSION['hash']."'";
	$result = query_exec($cn,$sql);
	$admin = result_assoc($result);

	session_destroy();

	header("Location:./index.php");
	db_close ($cn);

?>