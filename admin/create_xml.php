<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	
	//XML作成
	function create_xml($cn,$lang,$Tpl) {
		
		//現地時間取得
		$now = get_now($lang);
		$Tpl->assign("lang",$lang);

		$dirlang = ($lang == 'jpn' || $lang == 'eng') ? '' : '/'.$lang;

		//記事本体
//		$sql = "select id,lang,category,to_char(date,'yyyy/mm/dd') as date,to_char(date,'yyyy') as year,title,url,url_target,file_name from news where open_flg = 1 and top_flg = 1 and (to_char(date_open,'yyyymmddhh24miss') is null or to_char(date_open,'yyyymmddhh24miss') <= '".$now."') and (to_char(date_close,'yyyymmddhh24miss') is null or to_char(date_close,'yyyymmddhh24miss') > '".$now."') and lang = '".$lang."' order by date desc, date_insert desc";
		//10.08.26 追加
		$sql = "select id,lang,category,news_category,to_char(date,'yyyy/mm/dd') as date,to_char(date,'yyyy') as year,title,url,url_target,file_name,news_category from news where open_flg = 1 and top_flg = 1 and (to_char(date_open,'yyyymmddhh24miss') is null or to_char(date_open,'yyyymmddhh24miss') <= '".$now."') and (to_char(date_close,'yyyymmddhh24miss') is null or to_char(date_close,'yyyymmddhh24miss') > '".$now."') and lang = '".$lang."' order by date desc, date_insert desc";
		$result = query_exec($cn,$sql);
		$news = result_all($result);
/*
		if(is_array($news)){
			$news = exchange_xml_view($news);
		}
*/
		//ニュースカテゴリ
		$sql = "select * from news_category";
		$result = query_exec($cn,$sql);
		$news_category = result_all($result);
		$aryCategory = array();
		//IDベースにする
		if(is_array($news_category)){
			foreach($news_category as $category){
				$aryCategory[$category['id']] = $category;
			}
		}
		$Tpl->assign("category",$aryCategory);
		
		if($news != ""){
			$Tpl->assign("news",$news);
			$body = $Tpl->fetch($lang.'/home_list.tpl.html');
			$filename = $dirlang.'/shared/include/home_list.php';
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
				unlink($_SERVER['DOCUMENT_ROOT'].$filename);
			}
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
			fwrite($fp,$body);
			fclose($fp);
		}else {
			$body = $Tpl->fetch($lang.'/home_list.tpl.html');
			$filename = $dirlang.'/shared/include/home_list.php';
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
				unlink($_SERVER['DOCUMENT_ROOT'].$filename);
			}
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
			fwrite($fp,$body);
			fclose($fp);
		}
		
		//iPhone,iPad用-----------------------------------------------
		$lang_select = JPN_LANG;
		$charset = JPN_CHARSET;
		$Tpl->assign("charset",$charset);
		$Tpl->assign("lang",$lang);
/*
		$body = $Tpl->fetch('news_ipad.tpl.html');
		$body = mb_convert_encoding($body,$lang_select,"UTF-8");
		$filename = $dirlang.'/'.'index_ipad.htm';
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
			unlink($_SERVER['DOCUMENT_ROOT'].$filename);
		}
		$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
		fwrite($fp,$body);
		fclose($fp);
*/		
		if($lang == 'jpn' || $lang == 'eng'){
			$body = $Tpl->fetch($lang.'/smt/news_iphone.tpl.html');
			$filename = $dirlang.'/smt/shared/include/news_iphone.php';
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
				unlink($_SERVER['DOCUMENT_ROOT'].$filename);
			}
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
			fwrite($fp,$body);
			fclose($fp);
		}
		//------------------------------------------------------------
	}
	
?>