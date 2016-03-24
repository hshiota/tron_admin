<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	require_once("./auth.php");

	
	//IDチェック
	function id_check($post,$select,$lang,$cn){
		$error_flg = true;
		
		/*----------タイトル----------*/
		if($post['id'] != ""){
			if(!num_check($post['id'])){
				$error_flg = false;
			}else {
				$sql = "select count(*) from news where category = '".$select."' and lang = '".$lang."' and id = ".$post['id'];
				$result = query_exec($cn,$sql);
				$news = result_assoc($result);
				if($news['count'] < 1){
					$error_flg = false;
				}
			}
		}
		return($error_flg);
	}
	//パーツIDチェック
	function parts_id_check($post,$article_id,$cn){
		$error_flg = true;
		
		/*----------タイトル----------*/
		if($post['id'] != ""){
			if(!num_check($post['id'])){
				$error_flg = false;
			}else {
				$sql = "select count(*) from parts where article_id = ".$article_id." and id = ".$post['id'];
				$result = query_exec($cn,$sql);
				$parts = result_assoc($result);
				if($parts['count'] < 1){
					$error_flg = false;
				}
			}
		}
		return($error_flg);
	}
	
?>