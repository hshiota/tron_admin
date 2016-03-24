<?php 
	$Tpl->assign("lang",$lang);
	$post = mb_convert_in($_POST);

	$mode = array("write");
	$i = 0;
	$max = 1;
	while($i < $max){
		if($post['mode_'.$mode[$i]."_x"] != "" || $post['mode_'.$mode[$i]] != ""){
			$class = "edit_".$mode[$i];
			$class($Tpl,$cn,$lang,$select);
			break;
		}
		$i++;
	}
	//特に何も選ばれてなければ入力画面へ
	if($i >= $max){
		edit_confirm($Tpl,$cn,$lang,$select);
	}

	db_close ($cn);
	/*------------------------------------------------------*/
	//確認画面
	/*------------------------------------------------------*/
	function edit_confirm($Tpl,$cn,$lang,$select){
		if($_POST != ""){
			$post = mb_convert_in($_POST);
		}

		if($post['id'] != ""){
			$id_check = id_check($post,$select,$lang,$cn);
			if($id_check == false){
				header("Location:".LIST_PAGE);
			}
			//記事本体
			$sql = "select id,lang,category,to_char(date,'yyyy') as year,to_char(date,'mm') as mon,to_char(date,'dd') as day,title,url,url_target,detail_title,to_char(date_open,'yyyy') as open_date_year,to_char(date_open,'mm') as open_date_mon,to_char(date_open,'dd') as open_date_day,to_char(date_open,'hh24') as open_date_hour,to_char(date_open,'mi') as open_date_minute,to_char(date_close,'yyyy') as close_date_year,to_char(date_close,'mm') as close_date_mon,to_char(date_close,'dd') as close_date_day,to_char(date_close,'hh24') as close_date_hour,to_char(date_close,'mi') as close_date_minute,open_flg,top_flg from news where id = ".$post['id'];
			$result = query_exec($cn,$sql);
			$news = result_assoc($result);
			if($news != ""){
				$value_news = exchange_view($news);
			}
			if(count($news) > 0){
				//記事パーツ
				$sql = "select * from parts where article_id = ".$post['id']." order by sort";
				$result = query_exec($cn,$sql);
				$news_parts = result_all($result);
				if($news_parts != ""){
					$value_news_parts = exchange_view($news_parts);
					$value_news_m_parts = exchange_m_hidden($news_parts);
				}
			}else {
				header("Location:".LIST_PAGE);
			}
			if(is_array($news)){
				foreach($value_news as $key => $val){
					if($key == "open_flg" || $key == "top_flg"){
						if($val == 0){
							$val = 2;
						}
					}
					$Tpl->assign($key,$val);
				}
			}
			if(is_array($news_parts)){
				$parts_list = array();
				$link_cnt = 1;
				$pdf_cnt = 1;
				foreach($news_parts as $key => $val){
					$parts_key = $key+1;
					switch($news_parts[$key]['data_type']){
						case "main":
							$parts_list[$parts_key]['parts_id'] = $value_news_parts[$key]['id'];
							$parts_list[$parts_key]['type'] = $value_news_parts[$key]['data_type'];
							$parts_list[$parts_key]['sort'] = $value_news_parts[$key]['sort'];
							$parts_list[$parts_key]['main'] = $value_news_m_parts[$key]['main_text'];
							break;
						case "caption":
							$parts_list[$parts_key]['parts_id'] = $value_news_parts[$key]['id'];
							$parts_list[$parts_key]['type'] = $value_news_parts[$key]['data_type'];
							$parts_list[$parts_key]['sort'] = $value_news_parts[$key]['sort'];
							$parts_list[$parts_key]['caption'] = $value_news_parts[$key]['caption'];
							break;
						case "img":
							$parts_list[$parts_key]['parts_id'] = $value_news_parts[$key]['id'];
							$parts_list[$parts_key]['type'] = $value_news_parts[$key]['data_type'];
							$parts_list[$parts_key]['sort'] = $value_news_parts[$key]['sort'];
							$parts_list[$parts_key]['img_name'] = $value_news_parts[$key]['img_name'];
							$align = img_align_tonum($value_news_parts[$key]['img_align']);
							$parts_list[$parts_key]['img_align'] = $align;
							$parts_list[$parts_key]['img_width'] = $value_news_parts[$key]['img_width'];
							$parts_list[$parts_key]['img_height'] = $value_news_parts[$key]['img_height'];
							$parts_list[$parts_key]['img_alt'] = $value_news_parts[$key]['img_alt'];
							break;
						case "link":
							if($link_key == "" || $link_cnt > PARTS_LINK_MAX || $parts_list[$link_key]['block_id'] != $value_news_parts[$key]['block_id']){
								$link_key = $key+1;
								$link_cnt = 1;
							}
							$parts_list[$link_key]['parts_id'.$link_cnt] = $value_news_parts[$key]['id'];
							$parts_list[$link_key]['type'] = $value_news_parts[$key]['data_type'];
							$parts_list[$link_key]['sort'] = $value_news_parts[$key]['sort'];
							$parts_list[$link_key]['link_text'.$link_cnt] = $value_news_parts[$key]['link_text'];
							$parts_list[$link_key]['link_url'.$link_cnt] = $value_news_parts[$key]['link_url'];
							$parts_list[$link_key]['link_target'.$link_cnt] = $value_news_parts[$key]['link_target'];
							$link_cnt++;
							break;
						case "pdf":
							if($pdf_key == "" || $pdf_cnt > PARTS_PDF_MAX || $parts_list[$pdf_key]['block_id'] != $value_news_parts[$key]['block_id']){
								$pdf_key = $key+1;
								$pdf_cnt = 1;
							}
							$parts_list[$pdf_key]['parts_id'.$pdf_cnt] = $value_news_parts[$key]['id'];
							$parts_list[$pdf_key]['type'] = $value_news_parts[$key]['data_type'];
							$parts_list[$pdf_key]['sort'] = $value_news_parts[$key]['sort'];
							$parts_list[$pdf_key]['pdf_text'.$pdf_cnt] = $value_news_parts[$key]['pdf_text'];
							$parts_list[$pdf_key]['pdf_name'.$pdf_cnt] = $value_news_parts[$key]['pdf_name'];
							$parts_list[$pdf_key]['pdf_size'.$pdf_cnt] = $value_news_parts[$key]['pdf_size'];
							$pdf_cnt++;
							break;
					}
				}
			}
		}else {
			if($post['id'] == ""){
				header("Location:".LIST_PAGE);
			}
		}
		if(is_array($parts_list)){
			$parts = "";
			uasort($parts_list,"sort_new");
			$return_parts = view_confirm($parts_list,$post,$select,$lang);
			if($return_parts[2][1] == false){
				header("Location:".LIST_PAGE);
			}
			$Tpl->assign("parts",$return_parts[0]);
			$hidden .= $return_parts[1];
		}
		if($post != ""){
			$Tpl->assign("id",$post['id']);
			$post = exchange_hidden($post);
			$view_post = exchange_view($post);
			foreach($view_post as $key => $val){
				if($key == "mon" || $key == "day"){
					$Tpl->assign($key,str_pad($val,2,0,STR_PAD_LEFT));
				}else {
					$Tpl->assign($key,$val);
				}
			}
			$Tpl->assign("date",$view_post['year']."/".str_pad($view_post['mon'],2,0,STR_PAD_LEFT)."/".str_pad($view_post['day'],2,0,STR_PAD_LEFT));
			foreach($post as $key => $val){
				if(!preg_match("/^mode\_/",$key)){
					if(is_array($val)){
						foreach($val as $key2 => $val2){
							$hidden .= "<input type=\"hidden\" name=\"".$key."[]\" value=\"".$val2."\" />\n";
						}
					}else {
						$hidden .= "<input type=\"hidden\" name=\"".$key."\" value=\"".$val."\" />\n";
					}
				}
			}
			$Tpl->assign("hidden",$hidden);
		}
		
		$Tpl->register_outputfilter("mb_convert_out");
		$Tpl->display('admin/news_confirm.tpl.html');
	}

	/*------------------------------------------------------*/
	//完了画面
	/*------------------------------------------------------*/
	function edit_write($Tpl,$cn,$lang,$select){
		if($_POST != ""){
			$post = mb_convert_in($_POST);
			$post = exchange_sql($post);
		}
		//IDチェック
		$id_check = id_check($post,$select,$lang,$cn);
		if($id_check == false){
			header("Location:".LIST_PAGE);
		}
		//記事本体取得
		$sql = "select id,category,to_char(date,'yyyy') as year,file_name from news where id = ".$post['id'];
		$result = query_exec($cn,$sql);
		$news = result_assoc($result);
		//記事本体削除
		$sql = "delete from news where id = ".$post['id'];
		$result = query_exec($cn,$sql);
		if($result == true){
			//記事パーツ
			$sql = "select id,img_name,pdf_name from parts where article_id = ".$post['id']." order by sort";
			$result = query_exec($cn,$sql);
			$parts = result_all($result);
			//記事パーツ削除
			$sql = "delete from parts where article_id = ".$post['id'];
			query_exec($cn,$sql);

			//画像、PDFの削除
			if(is_array($parts)){
				foreach($parts as $key => $val){
					if($parts[$key]['img_name'] != ""){
						temp_delete($parts[$key]['img_name']);
					}
					if($parts[$key]['pdf_name'] != ""){
						temp_delete($parts[$key]['pdf_name']);
					}
				}
			}
			
			//HTMLの削除
			delete_html($news['file_name'],$post['id'],$cn);
			//HTMLリスト再生成
			restructure_del_html($cn,$lang,$news['category'],$news['year'],$Tpl);
			//リスト再生成
			create_list($cn,$lang,$news['category'],$Tpl);
			//xmlの再生成
			create_xml($cn,$lang,$Tpl);
			//RSSの再生成
			create_rss($cn,$lang,$Tpl);
		}
		header("Location:".LIST_PAGE."?year=".$news['year']);
	}
?>