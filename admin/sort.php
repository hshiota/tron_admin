<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	require_once("./auth.php");
	
	//パーツの入れ替え
	function parts_down($parts,$post) {
		if(is_array($parts)){
			foreach($parts as $key => $val){
				if(next($parts) != false){
					$next_parts = current($parts);
				}else {
					$next_parts = end($parts);
				}
				if($post['mode_parts_down'.$key] != ""){
					$prev_sort = $parts[$key]['sort'];
					$next_sort = $next_parts['sort'];
					$parts[$key]['sort'] = $next_sort;
					$next_parts['sort'] = $prev_sort;
					$parts[key($parts)] = $next_parts;
				}
			}
		}
		return $parts;
	}


?>