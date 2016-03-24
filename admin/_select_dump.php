<?php
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	$cn = db_connect();

	$sql = "select * from news where lang = 'jpn' and category = 'news' order by date";
	$result = query_exec($cn,$sql);
	$news = result_all($result);
	
	if(is_array($news)){
		foreach($news as $key => $val){
			//記事パーツ
			$sql = "select * from parts where article_id = '".$val['id']."' order by sort";
			$result = query_exec($cn,$sql);
			$parts = result_all($result);
			
			$news[$key]['parts'] = $parts;
		}
	}
	
	$fp = fopen("./dump_news.txt","w");
	fwrite($fp,print_r($news,true));
	fclose($fp);

	$sql = "select * from news where lang = 'jpn' and category = 'ir' order by date";
	$result = query_exec($cn,$sql);
	$news = result_all($result);
	
	if(is_array($news)){
		foreach($news as $key => $val){
			//記事パーツ
			$sql = "select * from parts where article_id = '".$val['id']."' order by sort";
			$result = query_exec($cn,$sql);
			$parts = result_all($result);
			
			$news[$key]['parts'] = $parts;
		}
	}
	
	$fp = fopen("./dump_ir.txt","w");
	fwrite($fp,print_r($news,true));
	fclose($fp);
	
	db_close ($cn);

?>
