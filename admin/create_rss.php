<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	
	//rss作成
	function create_rss($cn,$lang,$Tpl) {
		//現地時間取得
		$now = get_now($lang);
		$Tpl->assign("lang",$lang);

		if($lang == 'jpn' || $lang == 'eng'){
			$dirlang =  '';
		}else{
			return false;
		}

		//記事本体
		$sql = "select * from news where open_flg = 1 and (to_char(date_open,'yyyymmddhh24miss') is null or to_char(date_open,'yyyymmddhh24miss') <= '".$now."') and (to_char(date_close,'yyyymmddhh24miss') is null or to_char(date_close,'yyyymmddhh24miss') > '".$now."') and lang = '".$lang."' order by date desc, date_insert desc";
		$result = query_exec($cn,$sql);
		$news = result_all($result);
		if(is_array($news)){
			$news = exchange_xml_view($news);
		}

		//全部
		$lastday = "";
		if(is_array($news)){
			foreach($news as $item){
				$tmpday = getTimestamp($item['date']);
				if($lastday == ""){
					$lastday = $tmpday;
				}else if($lastday < $tmpday) {
					$lastday = $tmpday;
				}
			}
		}
		if($lastday){
			$Tpl->assign("lastday",date("Y-m-d H:i:s",$lastday));
		}
		$Tpl->assign("url",($lang == 'jpn') ? "http://www.tel.co.jp/news/" : "http://www.tel.com/news/");
		$Tpl->assign("title",($lang == 'jpn') ? "全ての最新情報" : "All News");

		
		$aryAll = array();//全て
		$aryIr = array();//IRのみ
		if(is_array($news)){
			$same_check = array();
			foreach($news as $item){
				if($item['category'] == "ir"){
					$aryIr[] = $item;
					$same_check[] = $item['date'].$item['title'];
				}
			}
			foreach($news as $item){
				if($item['category'] == "news"){
					if(!in_array($item['date'].$item['title'], $same_check)){
						$aryAll[] = $item;
					}
				}else{
					$aryAll[] = $item;
				}
			}
		}

		$Tpl->assign("news",$aryAll);
		$body = $Tpl->fetch($lang.'/rss/news_rss.tpl.xml');
		$filename = $dirlang.'/rss/all.xml';
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
			unlink($_SERVER['DOCUMENT_ROOT'].$filename);
		}
		$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
		fwrite($fp,$body);
		fclose($fp);
		
		$lastday = "";
		if(is_array($aryIr)){
			foreach($aryIr as $item){
				$tmpday = getTimestamp($item['date']);
				if($lastday == ""){
					$lastday = $tmpday;
				}elseif($lastday < $tmpday) {
					$lastday = $tmpday;
				}
			}
		}
		if($lastday){
			$Tpl->assign("lastday",date("Y-m-d H:i:s",$lastday));
		}
		$Tpl->assign("url",($lang == 'jpn') ? "http://www.tel.co.jp/ir/" : "http://www.tel.com/ir/");
		$Tpl->assign("title",($lang == 'jpn') ? "IR情報" : "Investor Relations ");
		$Tpl->assign("news",$aryIr);
		$body = $Tpl->fetch($lang.'/rss/news_rss.tpl.xml');
		$filename = $dirlang.'/rss/ir.xml';
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
			unlink($_SERVER['DOCUMENT_ROOT'].$filename);
		}
		$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
		fwrite($fp,$body);
		fclose($fp);
		
		//ニュースカテゴリごと
		$Tpl->assign("url",($lang == 'jpn') ? "http://www.tel.co.jp/news/" : "http://www.tel.com/news/");
		$Tpl->assign("url","http://www.tel.co.jp/news/");
		$sql = "select * from news_category";
		$result = query_exec($cn,$sql);
		$news_category = result_all($result);
		$aryCategory = array();
		//keyベースにする
		if(is_array($news_category)){
			foreach($news_category as $category){
				$aryCategory[$category['key']] = $category;
			}
		}
		$aryNewsCategory = array();
		if(is_array($news_category)){
			foreach($news_category as $category){
				$aryNewsCategory[$category['key']] = array();
				if(is_array($news)){
					foreach($news as $item){
						if($item['news_category'] == $category['id']){
							$aryNewsCategory[$category['key']][] = $item;
						}
					}
				}
			}
		}
		if(is_array($aryNewsCategory)){
			foreach($aryNewsCategory as $category => $item){
				$lastday = "";
				if(is_array($item)){
					foreach($item as $itemitem){
						$tmpday = getTimestamp($itemitem['date']);
						if($lastday == ""){
							$lastday = $tmpday;
						}else if($lastday < $tmpday) {
							$lastday = $tmpday;
						}
					}
				}
				if($lastday){
					$Tpl->assign("lastday",date("Y-m-d H:i:s",$lastday));
				}
				$Tpl->assign("title",($lang == 'jpn') ? $aryCategory[$category]['name_ja'] : $aryCategory[$category]['name_en']);
				$Tpl->assign("news",$item);
				$body = $Tpl->fetch($lang.'/rss/news_rss.tpl.xml');
				$filename = $dirlang.'/rss/news_'.$category.'.xml';
				if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
					unlink($_SERVER['DOCUMENT_ROOT'].$filename);
				}
				$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
				fwrite($fp,$body);
				fclose($fp);
			}
		}
	}
	function getTimestamp($date)
	{
		if($date != ""){
			//日付を分割
			$aryDate = explode("-",$date);
			$aryDate[] = explode(" ",$aryDate[2]);
			$aryDate[] = explode(":",$aryDate[3]);
			//タイムスタンプを取得
			$timestamp = time($aryDate[4],$aryDate[5],$aryDate[6],$aryDate[1],$aryDate[2],$aryDate[0]);
		}
		
		return $timestamp;
	}
	
?>