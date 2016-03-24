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
	$Tpl->assign("action","id_delete.php");
	//現在のモード
	$Tpl->assign("mode","delete");

	$post = $_POST;

	$mode = array("write");
	$i = 0;
	$max = 1;
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
		edit_confirm($Tpl,$cn,array(),array());
	}

	db_close ($cn);
	
	/*------------------------------------------------------*/
	//確認画面
	/*------------------------------------------------------*/
	function edit_confirm($Tpl,$cn){
		if($_POST != ""){
			$post = $_POST;
		}

		//IDチェック
		$check = check_id($post,$cn);
		if($check == false){
			header("Location:id_edit.php");
		}
		if($post['id'] != ""){
			$sql = "select id,authority,user_id as userid,password from admin where id = ".$post['id'];
			$result = query_exec($cn,$sql);
			$id = result_assoc($result);
			if(is_array($id)){
				if($id['authority'] != "Admin"){
					foreach($id as $key => $val){
						if($key == "password"){
							$Tpl->assign($key."_conf",$val);
						}
						$Tpl->assign($key,$val);
					}
				}else {
					header("Location:id_edit.php");
				}
			}
		}

		$hidden = "<input type=\"hidden\" name=\"id\" value=\"".$post['id']."\" />\n";
		$Tpl->assign("hidden",$hidden);
		$Tpl->assign("id",$post['id']);
		
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
		//IDチェック
		$check = check_id($post,$cn);
		if($check == false){
			header("Location:id_edit.php");
		}
		$sql = "select authority from admin where id = ".$post['id'];
		$result = query_exec($cn,$sql);
		$id = result_assoc($result);
		if($id['authority'] == "Admin"){
			header("Location:id_edit.php");
		}
		
		$sql = "delete from admin where id = ".$post['id'];
		query_exec($cn,$sql);
		
		header("Location:id_edit.php");
	}
?>