<?php 
	//表示年のリスト
	$sql = "select distinct on (to_char(date,'yyyy')) to_char(date,'yyyy') as date from news where lang = '".$_SESSION['code']."' and category = '".$select."' order by date desc";
	$result = query_exec($cn,$sql);
	$year_list = result_all($result);
	if(is_array($year_list)){
		$list_y = array();
		foreach($year_list as $key => $val){
			array_push($list_y,$year_list[$key]['date']);
		}
	}
	if(is_array($list_y) && $list_y != ""){
		$Tpl->assign("year_list",$list_y);
	}else {
		$list_y_now = array(date(Y));
		$Tpl->assign("year_list",$list_y_now);
	}

	//表示年の設定
	$get = mb_convert_in($_GET);
	if($get['year'] != ""){
		if(is_array($list_y)){
			$check_flg = false;
			foreach($list_y as $key => $val){
				if($get['year'] == $val){
					$check_flg = true;
					break;
				}
			}
			if($check_flg == true){
				$year = $get['year'];
			}else {
				$year = $list_y[0];
			}
		}else {
			$year = date(Y);
		}
	}else {
		if($list_y[0] != ""){
			$year = $list_y[0];
		}else {
			$year = date(Y);
		}
	}
	$Tpl->assign("year",$year);

	$now = get_now($_SESSION['code']);
	$Tpl->assign("now",$now);
	
	//記事リスト取得
	$sql = "select id,lang,user_id,to_char(date,'yyyy/mm/dd') as date,to_char(date,'yyyy') as year,title,url,to_char(date_open,'yyyy/mm/dd hh24:mi:ss') as date_open,to_char(date_open,'yyyymmddhh24miss') as open,to_char(date_close,'yyyy/mm/dd hh24:mi:ss') as date_close,to_char(date_close,'yyyymmddhh24miss') as close,open_flg,top_flg,file_name from news where to_char(date,'yyyy') = '".$year."' and lang = '".$_SESSION['code']."' and category = '".$select."' order by date desc, date_insert desc";
	$result = query_exec($cn,$sql);
	$list = result_all($result);
	
	if(is_array($list)){
		$list = exchange_view($list);
	}
	
	$Tpl->assign("list",$list);
	
	$Tpl->register_outputfilter("mb_convert_out");
	$Tpl->display('admin/news_edit.tpl.html');

	db_close ($cn);
?>