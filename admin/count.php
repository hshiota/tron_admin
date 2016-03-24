<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	
	// 日付別連番作成
	function create_count($id,$date,$date_file,$lang,$select,$cn) {
		$count = 1;
		$sql = "select id,file_name from news where to_char(date,'yyyy/mm/dd') = '".$date."' and lang = '".$lang."' and category = '".$select."' order by id desc";
		$result = query_exec($cn,$sql);
		$file = result_all($result);
		
		if(is_array($file)){
			$no = array();
			foreach($file as $key => $val){
				preg_match("/.+\/".$date_file."\_([0-9]+)\.htm$/",$file[$key]['file_name'],$match);
				if($match[1] != ""){
					if($file[$key]['id'] == $id){
						$now_count = $match[1];
						break;
					}else {
						array_push($no,$match[1]);
					}
				}
			}
		}
		if($now_count != ""){
			$count = $now_count;
		}else {
			if($no[0] != "" && is_array($no)){
				rsort($no);
				$count = $no[0] + 1;
			}
		}
		$count = str_pad($count,3,0,STR_PAD_LEFT);
		return $count;
	}
?>