<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	require_once("./auth.php");
	require_once("./id_insert_check.php");
	require_once("./include.php");

	session_cache_limiter('private, must-revalidate');
	session_name("tel_admin");
	session_start();
	
	$cn = db_connect();

	//権限チェック
	admin_check($_SESSION['user_id'],$cn);

	//送信先
	$Tpl->assign("action","id_update.php");
	//送信先（削除時）
	$Tpl->assign("action_delete","id_delete.php");
	//現在のモード
	$Tpl->assign("mode","update");

	$post = $_POST;

	$mode = array("confirm","return","write");
	$i = 0;
	$max = 3;
	while($i < $max){
		if($post['mode_'.$mode[$i]."_x"] != "" || $post['mode_'.$mode[$i]] != ""){
			if($mode[$i] == "return"){
				edit_form($Tpl,$cn,array(),array());
			}else {
				$class = "edit_".$mode[$i];
				$class($Tpl,$cn);
			}
			break;
		}
		$i++;
	}
	//特に何も選ばれていなければ入力画面へ。
	if($i >= $max){
		edit_form($Tpl,$cn,array(),array());
	}

	db_close ($cn);
	
	/*------------------------------------------------------*/
	//フォーム画面
	/*------------------------------------------------------*/
	function edit_form($Tpl,$cn,$error,$post){
		if($_POST != ""){
			$post = $_POST;
		}
		if($_GET != ""){
			$get = $_GET;
		}
		if($get['id'] != ""){
			$check = check_id($get,$cn);
			if($check == false){
				header("Location:id_edit.php");
			}else {
				$sql = "select id,authority,user_id as userid,password from admin where id = ".$get['id'];
				$result = query_exec($cn,$sql);
				$id = result_assoc($result);
				if(is_array($id)){
					foreach($id as $key => $val){
						if($key == "password"){
							$Tpl->assign($key."_conf",$val);
						}
						$Tpl->assign($key,$val);
					}
				}
			}
		}else {
			if($post['id'] == ""){
				header("Location:id_edit.php");
			}else {
				$check = check_id($post,$cn);
				if($check == false){
					header("Location:id_edit.php");
				}else {
					$sql = "select authority from admin where id = ".$post['id'];
					$result = query_exec($cn,$sql);
					$id = result_assoc($result);
					$Tpl->assign("authority",$id['authority']);
				}
			}
		}
		
		if($post != ""){
			$value_post = exchange_hidden($post);
		}
		
		if($error[1] == false){
			$Tpl->assign("error_msg",$error[0]);
		}
		if($value_post != ""){
			foreach($value_post as $key => $val){
				$Tpl->assign($key,$val);
			}
		}
		
		$Tpl->display('admin/id_form.tpl.html');
	}

	/*------------------------------------------------------*/
	//確認画面
	/*------------------------------------------------------*/
	function edit_confirm($Tpl,$cn){
		if($_POST != ""){
			$post = $_POST;
		}

		if($post != ""){
			$value_post = exchange_hidden($post);
			$view_post = exchange_view($post);
		}

		//クエリチェック
		$check = form_check($post,$cn);
		if($check[1] == false){
			edit_form($Tpl,$cn,$check,$post);
			exit;
		}
		//IDチェック
		$check = check_id($post,$cn);
		if($check == false){
			header("Location:id_edit.php");
		}

		$hidden = "";
		

		if($post != ""){
			foreach($view_post as $key => $val){
				$Tpl->assign($key,$val);
			}
			foreach($value_post as $key => $val){
				if(!preg_match("/^mode\_/",$key)){
					if(is_array($val)){
						foreach($val as $key2 => $val2){
							$hidden .= "<input type=\"hidden\" name=\"".$key."[]\" value=\"".$val2."\" />\n";
						}
					}else {
						$hidden .= "<input type=\"hidden\" name=\"".$key."\" value=\"".$val."\" />\n";
					}
				}
			}
			$Tpl->assign("hidden",$hidden);
		}
		
		$Tpl->display('admin/id_confirm.tpl.html');
	}

	/*------------------------------------------------------*/
	//完了画面
	/*------------------------------------------------------*/
	function edit_write($Tpl,$cn){
		if($_POST != ""){
			$post = $_POST;
			$post = exchange_sql($post);
		}
		//クエリチェック
		$check = form_check($post,$cn);
		
		if($check[1] == false){
			edit_form($Tpl,$cn,$check,$post);
			exit;
		}
		//IDチェック
		$check = check_id($post,$cn);
		if($check == false){
			header("Location:id_edit.php");
		}
		
		$sql = "update admin set ";
		$sql .= "user_id = '".$post['userid']."',";
		$sql .= "password = '".$post['password']."',";
		$sql .= "date_update = '".date("Y-m-d H:i:s")."'";
		$sql .= " where id = ".$post['id'];
		query_exec($cn,$sql);
		
		header("Location:id_edit.php");
	}
?>