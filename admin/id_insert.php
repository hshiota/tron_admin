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
	$Tpl->assign("action","id_insert.php");
	//現在のモード
	$Tpl->assign("mode","insert");

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
		
		$sql = "insert into admin(";
		$sql .= "authority,";
		$sql .= "user_id,";
		$sql .= "password,";
		$sql .= "date_insert";
		$sql .= ") values (";
		$sql .= "'Operator',";
		$sql .= "'".$post['userid']."',";
		$sql .= "'".$post['password']."',";
		$sql .= "'".date("Y-m-d H:i:s")."'";
		$sql .= ")";
		query_exec($cn,$sql);
		
		header("Location:id_edit.php");
	}
?>