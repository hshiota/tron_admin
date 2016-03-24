<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");

	//HTML再作成
	function restructure_html($cn,$id,$Tpl) {
		//記事本体
		$sql = "select id,lang,category,to_char(date,'yyyy') as year from news where id = ".$id;
		$result = query_exec($cn,$sql);
		$news = result_assoc($result);
		
		$now = get_now($news['lang']);
		
		//登録した年が新しいかどうか
		$sql = "select count(*) from news where open_flg = 1 and (to_char(date_open,'yyyymmddhh24miss') is null or to_char(date_open,'yyyymmddhh24miss') <= '".$now."') and (to_char(date_close,'yyyymmddhh24miss') is null or to_char(date_close,'yyyymmddhh24miss') > '".$now."') and lang = '".$news['lang']."' and category = '".$news['category']."' and to_char(date,'yyyy') = ".$news['year'];
		$result = query_exec($cn,$sql);
		$year_cnt = result_assoc($result);
		if($year_cnt['count'] <= 1){
			$now = get_now($news['lang']);
			$sql = "select id,lang,category,to_char(date,'yyyy') as year,to_char(date_open,'yyyymmddhh24miss') as date_open,to_char(date_close,'yyyymmddhh24miss') as date_close,open_flg,url,file_name from news where url is null and open_flg = 1 and (to_char(date_open,'yyyymmddhh24miss') is null or to_char(date_open,'yyyymmddhh24miss') <= '".$now."') and (to_char(date_close,'yyyymmddhh24miss') is null or to_char(date_close,'yyyymmddhh24miss') > '".$now."') and lang = '".$news['lang']."' and category = '".$news['category']."'";
			$result = pg_query($cn,$sql);
			$news_list = pg_fetch_all($result);
			
			if(is_array($news_list)){
				foreach($news_list as $key => $val){
					//HTML作成
					create_html($cn,$news_list[$key]['id'],$Tpl);
				}
			}
		}
	}
	//HTML再作成(削除)
	function restructure_del_html($cn,$lang,$category,$year,$Tpl) {
		$sql = "select id,lang,category,to_char(date,'yyyy') as year,to_char(date_open,'yyyymmddhh24miss') as date_open,to_char(date_close,'yyyymmddhh24miss') as date_close,open_flg,url,file_name from news where lang = '".$lang."' and category = '".$category."'";
		$result = pg_query($cn,$sql);
		$news = pg_fetch_all($result);
		
		$now = get_now($lang);
		
		if(is_array($news)){
			foreach($news as $key => $val){
				if($news[$key]['url'] == "" && $news[$key]['open_flg'] == "1" && ($news[$key]['date_open'] == "" || $news[$key]['date_open'] <= $now) && ($news[$key]['date_close'] == "" || $news[$key]['date_close'] > $now)){
					//HTML作成
					create_html($cn,$news[$key]['id'],$Tpl);
				}
			}
		}
		
		//削除した年がまだ存在するかどうか
		$sql = "select count(*) from news where open_flg = 1 and (to_char(date_open,'yyyymmddhh24miss') is null or to_char(date_open,'yyyymmddhh24miss') <= '".$now."') and (to_char(date_close,'yyyymmddhh24miss') is null or to_char(date_close,'yyyymmddhh24miss') > '".$now."') and lang = '".$lang."' and category = '".$category."' and to_char(date,'yyyy') = '".$year."'";
		$result = query_exec($cn,$sql);
		$year_cnt = result_assoc($result);
		if($year_cnt['count'] < 1){
			if($category == "news"){
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$lang.'/'.$category.'/'.$year.'/index.htm')){
					unlink($_SERVER['DOCUMENT_ROOT'].'/'.$lang.'/'.$category.'/'.$year.'/index.htm');
					$dirpath = dirname($_SERVER['DOCUMENT_ROOT'].'/'.$lang.'/'.$category.'/'.$year.'/index.htm');
					@rmdir($dirpath);
				}
			}else if ($category == "ir"){
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/'.$lang.'/'.$category.'/news/'.$year.'/index.htm')){
					unlink($_SERVER['DOCUMENT_ROOT'].'/'.$lang.'/'.$category.'/news/'.$year.'/index.htm');
					$dirpath = dirname($_SERVER['DOCUMENT_ROOT'].'/'.$lang.'/'.$category.'/news/'.$year.'/index.htm');
					@rmdir($dirpath);
				}
			}
		}
	}
?>