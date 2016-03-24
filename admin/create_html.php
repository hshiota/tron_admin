<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/admin/tag_nl2br.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/admin/count.php");
	
	//HTML作成
	function create_html($cn,$id,$Tpl) {
		
		//記事本体
		$sql = "select id,lang,category,news_category,to_char(date,'yyyy/mm/dd') as date,to_char(date,'yyyy') as year,to_char(date,'mm') as month,to_char(date,'dd') as day,to_char(date,'mmdd') as date_file,title,url,detail_title,to_char(date_open,'yyyymmddhh24miss') as date_open,to_char(date_close,'yyyymmddhh24miss') as date_close,open_flg from news where id = ".$id;
		$result = query_exec($cn,$sql);
		$news = result_assoc($result);
		
		if(is_array($news)){
			$value_news = exchange_view($news);
		}
		
		//記事パーツ
		$sql = "select * from parts where article_id = ".$id." order by sort";
		$result = query_exec($cn,$sql);
		$parts = result_all($result);
		
		if(is_array($parts)){
			$value_parts = exchange_view($parts);
			$value_m_parts = exchange_m_hidden($parts);
		}

		//ニュースカテゴリ読み込み
		$sql = "select * from news_category order by id";
		$result = query_exec($cn,$sql);
		$news_category = result_all($result);
		$Tpl->assign("ary_news_category",$news_category);

		//現地時間取得
		$now = get_now($news['lang']);
		
		if($post['url'] == "" && $news['open_flg'] == "1" && ($news['date_open'] == "" || $news['date_open'] <= $now) && ($news['date_close'] == "" || $news['date_close'] > $now)){
			//現在のニュース選択
			$Tpl->assign("select",$news['category']);
			//ニュースカテゴリ
			$Tpl->assign("news_category",$news['news_category']);
			//文字コード
			switch($news['lang']){
				case "jpn" :
					$lang = JPN_LANG;
					$charset = JPN_CHARSET;
					break;
				case "eng" :
					$lang = ENG_LANG;
					$charset = ENG_CHARSET;
					break;
				case "tw" :
					$lang = TW_LANG;
					$charset = TW_CHARSET;
					break;
				case "kr" :
					$lang = KR_LANG;
					$charset = KR_CHARSET;
					break;
				default : 
					$lang = JPN_LANG;
					$charset = JPN_CHARSET;
					break;
			}
			$Tpl->assign("charset",$charset);
			$Tpl->assign("lang",$news['lang']);

			if(is_array($value_news)){
				foreach($value_news as $key => $val){
					$Tpl->assign($key,$val);
				}
			}
			
			$parts_list = array();
			if(is_array($parts)){
				foreach($parts as $key => $val){
					if($parts[$key]['data_type'] == "link" || $parts[$key]['data_type'] == "pdf"){
						if($parts[$key]['data_type'] == "link"){
							$parts_max = PARTS_LINK_MAX;
						}else if($parts[$key]['data_type'] == "pdf"){
							$parts_max = PARTS_PDF_MAX;
						}
						if($cnt == "" || $cnt > $parts_max || $parts[$key]['block_id'] != $parts_list[$link_key]['block_id']){
							$cnt = 1;
							$link_key = $key;
						}
						$parts_list[$link_key]['data_type'] = $value_parts[$key]['data_type'];
						$parts_list[$link_key]['sort'] = $value_parts[$key]['sort'];
						$parts_list[$link_key]['block_id'] = $value_parts[$key]['block_id'];
						$parts_list[$link_key]['list'][$cnt] = $value_parts[$key];
						$cnt++;
					}else {
						if($parts[$key]['data_type'] == "main"){
							$parts_list[$key] = $value_m_parts[$key];
						}else {
							$parts_list[$key] = $value_parts[$key];
						}
					}
				}
			}
			
			if(is_array($parts_list)){
				$parts_assign = "";
				foreach($parts_list as $key => $val){
					switch($parts_list[$key]['data_type']){
						case "main":
							$main = nl2br($parts_list[$key]['main_text']);
							$main = tag_nl2br($main);
							$parts_assign .= "<div class=\"section\">\n".$main."\n</div><!-- class=\"section\" -->\n";
							break;
						case "caption":
							$parts_assign .= "<h4 class=\"subheading\">".$parts_list[$key]['caption']."</h4>\n";
							break;
						case "img":
							switch($parts_list[$key]['img_align']){
								case "left" :
									$parts_assign .= "<div class=\"image_left\"><img src=\"".$parts_list[$key]['img_name']."\" width=\"".$parts_list[$key]['img_width']."\" height=\"".$parts_list[$key]['img_height']."\" alt=\"".$parts_list[$key]['img_alt']."\" /></div>\n";
									break;
								case "center" :
									$parts_assign .= "<div class=\"image\"><img src=\"".$parts_list[$key]['img_name']."\" width=\"".$parts_list[$key]['img_width']."\" height=\"".$parts_list[$key]['img_height']."\" alt=\"".$parts_list[$key]['img_alt']."\" /></div>\n";
									break;
								case "right" :
									$parts_assign .= "<div class=\"image_right\"><img src=\"".$parts_list[$key]['img_name']."\" width=\"".$parts_list[$key]['img_width']."\" height=\"".$parts_list[$key]['img_height']."\" alt=\"".$parts_list[$key]['img_alt']."\" /></div>\n";
									break;
							}
							break;
						case "link":
							$parts_link = "<div class=\"section\">\n";
							$parts_link .= "<ul>\n";
							for($i=1;$i<=PARTS_LINK_MAX;$i++){
								if($parts_list[$key]['list'][$i]['link_url'] != "" && $parts_list[$key]['list'][$i]['link_text'] != ""){
									$parts_link .= "<li class=\"mark\"><a href=\"".$parts_list[$key]['list'][$i]['link_url']."\"";
									if($parts_list[$key]['list'][$i]['link_target'] == 1){
										$parts_link .= " target=\"_blank\"";
									}
									$parts_link .= ">".$parts_list[$key]['list'][$i]['link_text']."</a></li>\n";
								}
							}
							$parts_link .= "</ul>\n";
							$parts_link .= "</div><!-- class=\"section\" -->\n";
							if($parts_link != "<div class=\"section\">\n<ul>\n</ul>\n</div><!-- class=\"section\" -->\n"){
								$parts_assign .= $parts_link;
							}
							break;
						case "pdf":
							$parts_pdf = "<div class=\"section\">\n";
							$parts_pdf .= "<ul>\n";
							for($i=1;$i<=PARTS_PDF_MAX;$i++){
								if($parts_list[$key]['list'][$i]['pdf_name'] != "" && $parts_list[$key]['list'][$i]['pdf_text'] != ""){
									$parts_pdf .= "<li class=\"pdf\"><a href=\"".$parts_list[$key]['list'][$i]['pdf_name']."\" target=\"_blank\">".$parts_list[$key]['list'][$i]['pdf_text']."(".$parts_list[$key]['list'][$i]['pdf_size']."KB)</a></li>\n";
								}
							}
							$parts_pdf .= "</ul>\n";
							$parts_pdf .= "</div><!-- class=\"section\" -->\n";
							if($parts_pdf != "<div class=\"section\">\n<ul>\n</ul>\n</div><!-- class=\"section\" -->\n"){
								$parts_assign .= $parts_pdf;
							}
							break;
					}
				}
				$Tpl->assign("parts",$parts_assign);
			}
			//表示年リスト
			$sql = "select distinct on (to_char(date,'yyyy')) to_char(date,'yyyy') as date from news where open_flg = 1 and (to_char(date_open,'yyyymmddhh24miss') is null or to_char(date_open,'yyyymmddhh24miss') <= '".$now."') and (to_char(date_close,'yyyymmddhh24miss') is null or to_char(date_close,'yyyymmddhh24miss') > '".$now."') and lang = '".$news['lang']."' and category = '".$news['category']."' order by date desc";
			$result = query_exec($cn,$sql);
			$year_list = result_all($result);
			if(is_array($year_list)){
				$list_y = array();
				foreach($year_list as $key => $val){
					array_push($list_y,$year_list[$key]['date']);
				}
			}
			$Tpl->assign("year_list",$list_y);
			
			$dirlang = ($news['lang'] == 'jpn' || $news['lang'] == 'eng') ? '' : '/'.$news['lang'];
			
			$body = $Tpl->fetch($news['lang'].'/'.$news['category'].'/news_detail.tpl.html');
			$body = mb_convert_encoding($body,$lang,"UTF-8");
			
			//記事の連番を取得
			$count = create_count($news['id'],$news['date'],$news['date_file'],$news['lang'],$news['category'],$cn);
			
			if($news['category'] == "news"){
				$dir_name = $dirlang.'/'.$news['category'].'/'.$news['year'];
				if(!file_exists($_SERVER['DOCUMENT_ROOT'].$dir_name)){
					mkdir($_SERVER['DOCUMENT_ROOT'].$dir_name,0777);
					chmod($_SERVER['DOCUMENT_ROOT'].$dir_name,0777);
				}
				$filename_insert = '/'.$news['category'].'/'.$news['year'].'/'.$news['date_file'].'_'.$count.'.htm';
				$filename = $dirlang.'/'.$news['category'].'/'.$news['year'].'/'.$news['date_file'].'_'.$count.'.htm';
			}else if($news['category'] == "ir"){
				$dir_name = $dirlang.'/'.$news['category'].'/news/'.$news['year'];
				if(!file_exists($_SERVER['DOCUMENT_ROOT'].$dir_name)){
					mkdir($_SERVER['DOCUMENT_ROOT'].$dir_name,0777);
					chmod($_SERVER['DOCUMENT_ROOT'].$dir_name,0777);
				}
				$filename_insert = '/'.$news['category'].'/news/'.$news['year'].'/'.$news['date_file'].'_'.$count.'.htm';
				$filename = $dirlang.'/'.$news['category'].'/news/'.$news['year'].'/'.$news['date_file'].'_'.$count.'.htm';
			}
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$filename)){
				unlink($_SERVER['DOCUMENT_ROOT'].$filename);
			}
			$fp = fopen($_SERVER['DOCUMENT_ROOT'].$filename,"w");
			fwrite($fp,$body);
			fclose($fp);

			//ファイル名登録
			$sql = "update news set file_name = '".$filename_insert."' where id = ".$id." and lang = '".$news['lang']."' and category = '".$news['category']."' and (file_name is null or file_name <> '".$filename_insert."')";
			query_exec($cn,$sql);
		}
	}
	
	//HTML削除
	function delete_html($path,$id,$cn){
		if($path != ""){
			if(file_exists($_SERVER['DOCUMENT_ROOT'].$path)){
				unlink($_SERVER['DOCUMENT_ROOT'].$path);
				$sql = "select id,lang,url,to_char(date_open,'yyyymmddhh24miss') as date_open,to_char(date_close,'yyyymmddhh24miss') as date_close,open_flg from news where id = ".$id;
				$result = query_exec($cn,$sql);
				$news = result_assoc($result);
				$now = get_now($news['lang']);
				if($news['url'] != "" || $news['open_flg'] != "1" || ($news['date_open'] != "" && $news['date_open'] > $now) || ($news['date_close'] != "" && $$news['date_close'] < $now)){
					//ファイル名削除
					$sql = "update news set file_name = null where id = ".$id;
					query_exec($cn,$sql);
				}
			}
			$dirpath = dirname($path);
			@rmdir($_SERVER['DOCUMENT_ROOT'].$dirpath);
		}
	}

?>