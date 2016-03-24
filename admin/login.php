<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	
	session_name("tel_admin");
	session_start();
	session_unset();
	$cn = db_connect();
	
	if($_POST != ""){
		$post = $_POST;
		$post = exchange_sql($post);
	}
	
	$sql = "select user_id,authority,hash from admin where user_id = '".$post['id']."' and password = '".$post['password']."'";
	$result = query_exec($cn,$sql);
	$admin = result_assoc($result);
	
	if($admin != ""){
		if($admin['hash'] != "") {
			$_SESSION['hash'] = $admin['hash'];
			$_SESSION['authority'] = $admin['authority'];
			$_SESSION['user_id'] = $admin['user_id'];
		} else {
			$_SESSION['hash'] = md5(time());
			$_SESSION['authority'] = $admin['authority'];
			$_SESSION['user_id'] = $admin['user_id'];
			$sql = "update admin set hash = '".$_SESSION['hash']."' where user_id = '".$post['id']."' and password = '".$post['password']."'";
			query_exec($cn,$sql);
		}
		header("Location:top.php");
	}else {
		//ID・PWが間違ってる場合
		$error_msg = array("\"ID\" or \"Password\" is wrong.");
		$Tpl->assign("error_msg",$error_msg);
		if($post != ""){
			$value_post = exchange_hidden($post);
			foreach($value_post as $key => $val){
				if($key != "password"){
					$Tpl->assign($key,$val);
				}
			}
		}
		$Tpl->display('admin/index.tpl.html');
	}
	
	db_close ($cn);
?>