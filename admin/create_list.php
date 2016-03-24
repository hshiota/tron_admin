<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");

	//HTML作成
	function create_list($cn,$lang,$select,$Tpl) {
		
		//現地時間取得
		$now = get_now($lang);

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

		//記事本体
//		$sql = "select id,lang,category,to_char(date,'yyyy/mm/dd') as date,to_char(date,'yyyy') as year,title,url,url_target,file_name from news where open_flg = 1 and (to_char(date_open,'yyyymmddhh24miss') is null or to_char(date_open,'yyyymmddhh24miss') <= '".$now."') and (to_char(date_close,'yyyymmddhh24miss') is null or to_char(date_close,'yyyymmddhh24miss') > '".$now."') and lang = '".$lang."' and category = '".$select."' order by date desc, date_insert desc";
		//10.08.26 追加
		$sql = "select id,lang,category,to_char(date,'yyyy/mm/dd') as date,to_char(date,'yyyy') as year,title,url,url_target,file_name,news_category from news where open_flg = 1 and (to_char(date_open,'yyyymmddhh24miss') is null or to_char(date_open,'yyyymmddhh24miss') <= '".$now."') and (to_char(date_close,'yyyymmddhh24miss') is null or to_char(date_close,'yyyymmddhh24miss') > '".$now."') and lang = '".$lang."' and category = '".$select."' order by date desc, date_insert desc";
		$result = query_exec($cn,$sql);
		$news = result_all($result);
		
		if(is_array($news)){
			$news = exchange_view($news);
			$news_list = array();
			foreach($news as $key => $val){
				$news_list[$news[$key]['year']][$key] = $val;
			}
		}
		if(is_array($news_list)){
			$news_list_reset = array();
			foreach($news_list as $key => $val){
				$i = 0;
				foreach($val as $key2 => $val2){
					$news_list_reset[$key][$i] = $val2;
					$i++;
				}
			}
		}
		
		//表示年リスト
		$sql = "select distinct on (to_char(date,'yyyy')) to_char(date,'yyyy') as date from news where open_flg = 1 and (to_char(date_open,'yyyymmddhh24miss') is null or to_char(date_open,'yyyymmddhh24miss') <= '".$now."') and (to_char(date_close,'yyyymmddhh24miss') is null or to_char(date_close,'yyyymmddhh24miss') > '".$now."') and lang = '".$lang."' and category = '".$select."' order by date desc";
		$result = query_exec($cn,$sql);
		$year_list = result_all($result);
		if(is_array($year_list)){
			$list_y = array();
			foreach($year_list as $key => $val){
				array_push($list_y,$year_list[$key]['date']);
			}
		}
		$Tpl->assign("year_list",$list_y);
		
		if(is_array($news_list_reset)){
			//現在のニュース選択
			$Tpl->assign("select",$select);
			//文字コード
			switch($lang){
				case "jpn" :
					$lang_select = JPN_LANG;
					$charset = JPN_CHARSET;
					break;
				case "eng" :
					$lang_select = ENG_LANG;
					$charset = ENG_CHARSET;
					break;
				case "tw" :
					$lang_select = TW_LANG;
					$charset = TW_CHARSET;
					break;
				case "kr" :
					$lang_select = KR_LANG;
					$charset = KR_CHARSET;
					break;
				default : 
					$lang_select = JPN_LANG;
					$charset = JPN_CHARSET;
					break;
			}
			$Tpl->assign("charset",$charset);
			$Tpl->assign("lang",$lang);

			$dirlang = ($lang == 'jpn' || $lang == 'eng') ? '' : '/'.$lang;

			//年別一覧
			foreach($news_list_reset as $key => $val){

				$Tpl->assign("news",$val);
				//記事の年
				$Tpl->assign("now_year",$key);
				
				
				$body = $Tpl->fetch($lang.'/'.$select.'/news_list.tpl.html');
				$body = mb_convert_encoding($body,$lang_select,"UTF-8");
				
				if($select == "news"){
					$dir_name = $dirlang.'/'.$select.'/'.$key;
					if(!file_exists($_SERVER['DOCUMENT_ROOT'].$dir_name)){
						mkdir($_SERVER['DOCUMENT_ROOT'].$dir_name,0777);
						chmod($_SERVER['DOCUMENT_ROOT'].$dir_name,0777);
					}
					$filename = $dirlang.'/'.$select.'/'.$key.'/index.htm';
				}else if($select == "ir"){
					$dir_name = $dirlang.'/'.$select.'/news/'.$key;
					if(!file_exists($_SERVER['DOCUMENT_ROOT'].$dir_name)){
						mkdir($_SERVER['DOCUMENT_ROOT'].$dir_name,0777);
						chmod($_SERVER['DOCUMENT_ROOT'].$dir_name,0777);
					}
					$filename = $dirlang.'/'.$select.'/news/'.$key.'/index.htm';
				}
				if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
					unlink($_SERVER['DOCUMENT_ROOT'].$filename);
				}
				$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
				fwrite($fp,$body);
				fclose($fp);
			}
			
			//最新一覧
			if($select == "news"){
				if($news != ""){
					$news = array_slice($news,0,NEWS_MAX);
				}
				$Tpl->assign("year",$list_y[0]);
				$Tpl->assign("news",$news);
				$body = $Tpl->fetch($lang.'/'.$select.'/news_archive.tpl.html');
				$body = mb_convert_encoding($body,$lang_select,"UTF-8");
				$filename = $dirlang.'/shared/include/'.$select.'_archive.php';
				if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
					unlink($_SERVER['DOCUMENT_ROOT'].$filename);
				}
				$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
				fwrite($fp,$body);
				fclose($fp);
				$body = $Tpl->fetch($lang.'/'.$select.'/news_index.tpl.html');
				$body = mb_convert_encoding($body,$lang_select,"UTF-8");
				$filename = $dirlang.'/shared/include/'.$select.'_list.php';
				if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
					unlink($_SERVER['DOCUMENT_ROOT'].$filename);
				}
				$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
				fwrite($fp,$body);
				fclose($fp);
			}else if($select == "ir"){
				if($news != ""){
					$news = array_slice($news,0,IR_MAX);
				}
				$Tpl->assign("year",$list_y[0]);
				$Tpl->assign("news",$news);
				$body = $Tpl->fetch($lang.'/'.$select.'/news_archive.tpl.html');
				$body = mb_convert_encoding($body,$lang_select,"UTF-8");
				$filename = $dirlang.'/shared/include/'.$select.'_archive.php';
				if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
					unlink($_SERVER['DOCUMENT_ROOT'].$filename);
				}
				$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
				fwrite($fp,$body);
				fclose($fp);
				$body = $Tpl->fetch($lang.'/'.$select.'/news_index.tpl.html');
				$body = mb_convert_encoding($body,$lang_select,"UTF-8");
				$filename = $dirlang.'/shared/include/'.$select.'_list.php';
				if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
					unlink($_SERVER['DOCUMENT_ROOT'].$filename);
				}
				$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
				fwrite($fp,$body);
				fclose($fp);
			}
		}else {
			//最新一覧
			if($select == "news"){
				if($news != ""){
					$news = array_slice($news,0,NEWS_MAX);
				}
				$body = $Tpl->fetch($lang.'/'.$select.'/news_archive.tpl.html');
				$body = mb_convert_encoding($body,$lang_select,"UTF-8");
				$filename = $dirlang.'/shared/include/'.$select.'_archive.php';
				if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
					unlink($_SERVER['DOCUMENT_ROOT'].$filename);
				}
				$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
				fwrite($fp,$body);
				fclose($fp);
				$body = " ";
				$body = mb_convert_encoding($body,$lang_select,"UTF-8");
				$filename = $dirlang.'/shared/include/'.$select.'_list.php';
				if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
					unlink($_SERVER['DOCUMENT_ROOT'].$filename);
				}
				$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
				fwrite($fp,$body);
				fclose($fp);
			}else if($select == "ir"){
				if($news != ""){
					$news = array_slice($news,0,IR_MAX);
				}
				$body = $Tpl->fetch($lang.'/'.$select.'/news_archive.tpl.html');
				$body = mb_convert_encoding($body,$lang_select,"UTF-8");
				$filename = $dirlang.'/shared/include/'.$select.'_archive.php';
				if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
					unlink($_SERVER['DOCUMENT_ROOT'].$filename);
				}
				$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
				fwrite($fp,$body);
				fclose($fp);
				$body = " ";
				$body = mb_convert_encoding($body,$lang_select,"UTF-8");
				$filename = $dirlang.'/shared/include/'.$select.'_list.php';
				if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
					unlink($_SERVER['DOCUMENT_ROOT'].$filename);
				}
				$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
				fwrite($fp,$body);
				fclose($fp);
			}
		}
	}

?>