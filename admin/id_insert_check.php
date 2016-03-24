<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	require_once("./auth.php");
	
	//権限チェック
	function admin_check($user,$cn){
		$sql = "select authority from admin where user_id = '".$user."'";
		$result = query_exec($cn,$sql);
		$auth = result_assoc($result);
		if($auth['authority'] != "Admin"){
			header("Location:./top.php");
		}
	}

	
	//入力チェック
	function form_check($post,$cn){
		$error_flg = true;
		$error_msg = array();
		
		/*----------登録状況----------*/
		if($post['userid'] != "" && $post['id'] == ""){
			$sql = "select count(*) from admin where authority <> 'Admin'";
			$result = query_exec($cn,$sql);
			$count = result_assoc($result);
			if($count['count'] >= OPERATOR_MAX){
				$error_flg = false;
				array_push($error_msg,"The number of operators cannot be increased any further.");
			}
		}
		
		/*----------ユーザID----------*/
		if($post['userid'] != ""){
			if(!eisu_check($post['userid'])){
				$error_flg = false;
				array_push($error_msg,"The input of \"UserID\" is wrong.");
			}else {
				if($post['id'] == ""){
					$sql = "select count(*) from admin where user_id = '".$post['userid']."'";
					$result = query_exec($cn,$sql);
					$count = result_assoc($result);
					if($count['count'] > 0){
						$error_flg = false;
						array_push($error_msg,"Input UserID has already been registered.");
					}
				}else {
					$sql = "select count(*) from admin where user_id = '".$post['userid']."' and id <> ".$post['id'];
					$result = query_exec($cn,$sql);
					$count = result_assoc($result);
					if($count['count'] > 0){
						$error_flg = false;
						array_push($error_msg,"Input UserID has already been registered.");
					}
				}
			}
		}else {
			$error_flg = false;
			array_push($error_msg,"Please input \"UserID\".");
		}
		/*----------パスワード----------*/
		if($post['password'] != ""){
			if(!eisu_check($post['password'])){
				$error_flg = false;
				array_push($error_msg,"The input of \"Password\" is wrong.");
			}
		}else {
			$error_flg = false;
			array_push($error_msg,"Please input \"Password\".");
		}
		/*----------パスワード確認用----------*/
		if($post['password_conf'] != ""){
			if($post['password_conf'] != $post['password']){
				$error_flg = false;
				array_push($error_msg,"\"Password (confirmation)\" is different from Password.");
			}
		}else {
			$error_flg = false;
			array_push($error_msg,"Please input \"Password (confirmation)\".");
		}

		return(array($error_msg,$error_flg));
	}
	
	//IDチェック
	function check_id($post,$cn){
		$error_flg = true;
		
		if($post['id'] != ""){
			if(!num_check($post['id'])){
				$error_flg = false;
			}else {
				$sql = "select count(*) from admin where id = ".$post['id'];
				$result = query_exec($cn,$sql);
				$count = result_assoc($result);
				if($count['count'] < 1){
					$error_flg = false;
				}
			}
		}else {
			$error_flg = false;
		}
		
		return($error_flg);
	}
	
?>