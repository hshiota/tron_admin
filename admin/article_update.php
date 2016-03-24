<?php 
	$Tpl->assign("lang",$lang);
	$post = mb_convert_in($_POST);

	//ニュースカテゴリ読み込み
	$sql = "select * from news_category order by id";
	$result = query_exec($cn,$sql);
	$news_category = result_all($result);
	$Tpl->assign("ary_news_category",$news_category);
	
	$mode = array("confirm","return","write");
	$i = 0;
	$max = 3;
	while($i < $max){
		if($post['mode_'.$mode[$i]."_x"] != "" || $post['mode_'.$mode[$i]] != ""){
			if($mode[$i] == "return"){
				edit_form($Tpl,$cn,$lang,$select,array(),array());
			}else {
				$class = "edit_".$mode[$i];
				$class($Tpl,$cn,$lang,$select);
			}
			break;
		}
		$i++;
	}
	//特に何も選ばれていなければ入力画面へ。
	if($i >= $max){
		edit_form($Tpl,$cn,$lang,$select,array(),array());
	}

	db_close ($cn);
	
	/*------------------------------------------------------*/
	//フォーム画面
	/*------------------------------------------------------*/
	function edit_form($Tpl,$cn,$lang,$select,$error,$post){
		if($_POST != ""){
			$post = mb_convert_in($_POST);
		}
		if($_GET != ""){
			$get = mb_convert_in($_GET);
		}
		
		$no = 1;
		$sort = 1;
		
		if($post != ""){
			$value_post = exchange_hidden($post);
		}
		
		if($get != ""){
			$get = exchange_hidden($get);
		}
		
		
		if($get['id'] != ""){
			$id_check = id_check($get,$select,$lang,$cn);
			if($id_check == false){
				header("Location:".LIST_PAGE);
			}
			//記事本体
//			$sql = "select id,lang,category,to_char(date,'yyyy') as year,to_char(date,'mm') as mon,to_char(date,'dd') as day,title,url,url_target,detail_title,to_char(date_open,'yyyy') as open_date_year,to_char(date_open,'mm') as open_date_mon,to_char(date_open,'dd') as open_date_day,to_char(date_open,'hh24') as open_date_hour,to_char(date_open,'mi') as open_date_minute,to_char(date_close,'yyyy') as close_date_year,to_char(date_close,'mm') as close_date_mon,to_char(date_close,'dd') as close_date_day,to_char(date_close,'hh24') as close_date_hour,to_char(date_close,'mi') as close_date_minute,open_flg,top_flg from news where id = ".$get['id'];
			//10.08.25 ニュースカテゴリを追加
			$sql = "select id,lang,category,to_char(date,'yyyy') as year,to_char(date,'mm') as mon,to_char(date,'dd') as day,title,url,url_target,detail_title,to_char(date_open,'yyyy') as open_date_year,to_char(date_open,'mm') as open_date_mon,to_char(date_open,'dd') as open_date_day,to_char(date_open,'hh24') as open_date_hour,to_char(date_open,'mi') as open_date_minute,to_char(date_close,'yyyy') as close_date_year,to_char(date_close,'mm') as close_date_mon,to_char(date_close,'dd') as close_date_day,to_char(date_close,'hh24') as close_date_hour,to_char(date_close,'mi') as close_date_minute,open_flg,top_flg,news_category from news where id = ".$get['id'];
			$result = query_exec($cn,$sql);
			$news = result_assoc($result);
			if($news != ""){
				$value_news = exchange_hidden($news);
			}
			if(count($news) > 0){
				//記事パーツ
				$sql = "select * from parts where article_id = ".$get['id']." order by sort";
				$result = query_exec($cn,$sql);
				$news_parts = result_all($result);
				if($news_parts != ""){
					$value_news_parts = exchange_hidden($news_parts);
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
							$parts_list[$parts_key]['main'] = $value_news_parts[$key]['main_text'];
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
							$align = img_align_tonum($news_parts[$key]['img_align']);
							$parts_list[$parts_key]['img_align'] = $align;
							$parts_list[$parts_key]['img_width'] = $value_news_parts[$key]['img_width'];
							$parts_list[$parts_key]['img_height'] = $value_news_parts[$key]['img_height'];
							$parts_list[$parts_key]['img_alt'] = $value_news_parts[$key]['img_alt'];
							break;
						case "link":
							if($link_key == "" || $link_cnt > PARTS_LINK_MAX || $parts_list[$link_key]['block_id'] != $news_parts[$key]['block_id']){
								$link_key = $key+1;
								$link_cnt = 1;
							}
							$parts_list[$link_key]['parts_id'.$link_cnt] = $value_news_parts[$key]['id'];
							$parts_list[$link_key]['type'] = $value_news_parts[$key]['data_type'];
							$parts_list[$link_key]['sort'] = $value_news_parts[$key]['sort'];
							$parts_list[$link_key]['link_text'.$link_cnt] = $value_news_parts[$key]['link_text'];
							$parts_list[$link_key]['link_url'.$link_cnt] = $value_news_parts[$key]['link_url'];
							$parts_list[$link_key]['link_target'.$link_cnt] = $value_news_parts[$key]['link_target'];
							$parts_list[$link_key]['block_id'] = $value_news_parts[$key]['block_id'];
							$link_cnt++;
							break;
						case "pdf":
							if($pdf_key == "" || $pdf_cnt > PARTS_PDF_MAX || $parts_list[$pdf_key]['block_id'] != $news_parts[$key]['block_id']){
								$pdf_key = $key+1;
								$pdf_cnt = 1;
							}
							$parts_list[$pdf_key]['parts_id'.$pdf_cnt] = $value_news_parts[$key]['id'];
							$parts_list[$pdf_key]['type'] = $value_news_parts[$key]['data_type'];
							$parts_list[$pdf_key]['sort'] = $value_news_parts[$key]['sort'];
							$parts_list[$pdf_key]['pdf_text'.$pdf_cnt] = $value_news_parts[$key]['pdf_text'];
							$parts_list[$pdf_key]['pdf_name'.$pdf_cnt] = $value_news_parts[$key]['pdf_name'];
							$parts_list[$pdf_key]['pdf_size'.$pdf_cnt] = $value_news_parts[$key]['pdf_size'];
							$parts_list[$pdf_key]['block_id'] = $value_news_parts[$key]['block_id'];
							$pdf_cnt++;
							break;
					}
				}
			}
		}else if ($post['id'] != ""){
			$id_check = id_check($post,$select,$lang,$cn);
			if($id_check == false){
				header("Location:".LIST_PAGE);
			}
		}else {
			header("Location:".LIST_PAGE);
		}
		
		/*----------既存パーツ----------*/
		if(is_array($value_post) && $parts_list == ""){
			$parts_list = array();
			$add_flg = 0;
			$parts_delete_cnt = 0;
			foreach($value_post as $key => $val){
				if(preg_match("/^parts([0-9]+)_([a-z|_|0-9]+)$/",$key,$match)){
					$parts_list[$match[1]][$match[2]] = $value_post[$key];
				}
				if(preg_match("/^mode_[a-z]+_add$/",$key)){
					$add_flg = 1;
				}
				if(preg_match("/^mode_parts_delete[0-9]+$/",$key)){
					$parts_delete_cnt++;
				}
			}
		}
		if(is_array($_FILES)){
			foreach($_FILES as $key => $val){
				if(preg_match("/^parts([0-9]+)_([a-z|_|0-9]+)$/",$key,$match)){
					$parts_list[$match[1]][$match[2]] = $_FILES[$key];
				}
			}
		}
		
		if(is_array($parts_list)){
			$parts_list = parts_down($parts_list,$value_post);//下へ移動
			uasort($parts_list,"sort_new");
			$j = 1;
			$now_parts_cnt = count($parts_list)-$parts_delete_cnt;
			$img_cnt = 1;
			$pdf_cnt = 1;
			foreach($parts_list as $key => $val){
				if($parts_list[$key]['type'] != ""){
					$assign_array = array();
					if($post['mode_parts_delete'.$key] == ""){
						$Tpl->assign("no",$key);
						array_push($assign_array,"no");
						$Tpl->assign("sort",$parts_list[$key]['sort']);
						array_push($assign_array,"sort");
						if($no < $key){
							$no = $key;
						}
						$sort = $parts_list[$key]['sort'];
						if($now_parts_cnt <= 1 && $add_flg == 0){
							$Tpl->assign("notdelete_flg",1);
							array_push($assign_array,"notdelete_flg");
							$Tpl->assign("notdown_flg",1);
							array_push($assign_array,"notdown_flg");
						}
						if($now_parts_cnt == $j && $add_flg == 0){
							$Tpl->assign("notdown_flg",1);
							array_push($assign_array,"notdown_flg");
						}
						foreach($parts_list[$key] as $key2 => $val2){
							if($key2 != "type" && $key2 != "sort"){
								$Tpl->assign($key2,$val2);
								array_push($assign_array,$key2);
							}
						}
						if($parts_list[$key]['type'] == "img"){
							if($parts_list[$key]['img']['error'] == 0 && $parts_list[$key]['img'] != ""){
								if($post['year'] != "" && $post['mon'] != "" && $post['day'] != ""){
									$date = $post['year'].(str_pad($post['mon'],2,0,STR_PAD_LEFT)).(str_pad($post['day'],2,0,STR_PAD_LEFT));
								}else {
									$date = date(Ymd);
								}
								$check = img_upload($date,$parts_list[$key]['img'],$select,$lang,$img_cnt);
								if($check[1] != false){
									$parts_list[$key]['path'] = $check[2];
									$parts_list[$key]['img_filename'] = $parts_list[$key]['img']['name'];
								}
							}
							if($parts_list[$key]['path'] != ""){
								$size = getimagesize($_SERVER['DOCUMENT_ROOT'].$parts_list[$key]['path']);
								$Tpl->assign("img_name",$parts_list[$key]['path']);
								$Tpl->assign("img_width",$size[0]);
								$Tpl->assign("img_height",$size[1]);
								$Tpl->assign("img_filename",$parts_list[$key]['img_filename']);
								array_push($assign_array,"img_name");
								array_push($assign_array,"img_width");
								array_push($assign_array,"img_height");
							}
							$img_cnt++;
						}
						if($parts_list[$key]['type'] == "pdf"){
							for($i=1;$i<=PARTS_PDF_MAX;$i++){
								if($parts_list[$key]['pdf_file'.$i]['error'] == 0 && $parts_list[$key]['pdf_file'.$i] != ""){
									if(preg_match("/^.+\.pdf$/",$parts_list[$key]['pdf_file'.$i]['name'])){
										if($post['year'] != "" && $post['mon'] != "" && $post['day'] != ""){
											$date = $post['year'].(str_pad($post['mon'],2,0,STR_PAD_LEFT)).(str_pad($post['day'],2,0,STR_PAD_LEFT));
										}else {
											$date = date(Ymd);
										}
										$check = pdf_upload($date,$parts_list[$key]['pdf_file'.$i],$select,$lang,$pdf_cnt);
										if($check[1] != false){
											$size = round($parts_list[$key]['pdf_file'.$i]['size'] / 1000);
											$parts_list[$key]['path'.$i] = $check[2];
											$parts_list[$key]['pdf_filename'.$i] = $parts_list[$key]['pdf_file'.$i]['name'];
											$parts_list[$key]['pdf_size'.$i] = $size;
										}
									}
								}
								if($parts_list[$key]['path'.$i] != ""){
									$size = round(filesize($_SERVER['DOCUMENT_ROOT'].$parts_list[$key]['path'.$i]) / 1000);
									$Tpl->assign("pdf_name".$i,$parts_list[$key]['path'.$i]);
									$Tpl->assign("pdf_filename".$i,$parts_list[$key]['pdf_filename'.$i]);
									$Tpl->assign("pdf_size".$i,$size);
									array_push($assign_array,"pdf_name".$i);
									array_push($assign_array,"pdf_filename".$i);
									array_push($assign_array,"pdf_size".$i);
								}
								$pdf_cnt++;
							}
						}
						$parts .= $Tpl->fetch('admin/parts/parts_'.$parts_list[$key]['type'].'.tpl.html');
						$Tpl->clear_assign($assign_array);
						$j++;
					}
				}
			}
			if($parts == ""){
				/*----------デフォルト本文パーツ----------*/
				$Tpl->assign("no",$no);
				$Tpl->assign("sort",$sort);
				$Tpl->assign("notdelete_flg",1);
				$Tpl->assign("notdown_flg",1);
				$parts = $Tpl->fetch('admin/parts/parts_main.tpl.html');
				$Tpl->assign("parts",$parts);
			}
		}
		$Tpl->assign("parts",$parts);
		
		/*----------追加本文パーツ----------*/
		if($post['mode_main_add'] != ""){
			$no++;
			$sort++;
			$Tpl->assign("no",$no);
			$Tpl->assign("sort",$sort);
			$Tpl->assign("notdown_flg",1);
			$parts .= $Tpl->fetch('admin/parts/parts_main.tpl.html');
			$Tpl->assign("parts",$parts);
		}
		/*----------追加見出しパーツ----------*/
		if($post['mode_caption_add'] != ""){
			$no++;
			$sort++;
			$Tpl->assign("no",$no);
			$Tpl->assign("sort",$sort);
			$Tpl->assign("notdown_flg",1);
			$parts .= $Tpl->fetch('admin/parts/parts_caption.tpl.html');
			$Tpl->assign("parts",$parts);
		}
		/*----------追加画像パーツ----------*/
		if($post['mode_img_add'] != ""){
			$no++;
			$sort++;
			$Tpl->assign("no",$no);
			$Tpl->assign("sort",$sort);
			$Tpl->assign("notdown_flg",1);
			$parts .= $Tpl->fetch('admin/parts/parts_img.tpl.html');
			$Tpl->assign("parts",$parts);
		}
		/*----------追加リンクパーツ----------*/
		if($post['mode_link_add'] != ""){
			$no++;
			$sort++;
			$Tpl->assign("no",$no);
			$Tpl->assign("sort",$sort);
			$Tpl->assign("notdown_flg",1);
			$parts .= $Tpl->fetch('admin/parts/parts_link.tpl.html');
			$Tpl->assign("parts",$parts);
		}
		/*----------追加PDFパーツ----------*/
		if($post['mode_pdf_add'] != ""){
			$no++;
			$sort++;
			$Tpl->assign("no",$no);
			$Tpl->assign("sort",$sort);
			$Tpl->assign("notdown_flg",1);
			$parts .= $Tpl->fetch('admin/parts/parts_pdf.tpl.html');
			$Tpl->assign("parts",$parts);
		}

		//時間
		$hour = get_hour();
		$Tpl->assign("hour",$hour);
		//分
		$minute = get_minute();
		$Tpl->assign("min",$minute);

		
		if($error[1] == false){
			$Tpl->assign("error_msg",$error[0]);
		}
		if($value_post != ""){
			foreach($value_post as $key => $val){
				$Tpl->assign($key,$val);
			}
		}
		//プレビュー処理
//		$post = preview_post($post,$parts_list,$select,$lang);
		//postをセッション値へ
//		$_SESSION['post'] = $post;

		$Tpl->register_outputfilter("mb_convert_out");
		$Tpl->display('admin/news_form.tpl.html');
	}

	/*------------------------------------------------------*/
	//確認画面
	/*------------------------------------------------------*/
	function edit_confirm($Tpl,$cn,$lang,$select){
		if($_POST != ""){
			$post = mb_convert_in($_POST);
		}

		if($post != ""){
			$value_post = exchange_hidden($post);
			$view_post = exchange_view($post);
			$value_m_post = exchange_m_hidden($post);
		}

		//IDチェック
		$id_check = id_check($post,$select,$lang,$cn);
		if($id_check == false){
			header("Location:".LIST_PAGE);
		}
		//クエリチェック
		$check = form_check($post,$cn,$lang,$select);
		
		
		//パーツクエリチェック
		if(is_array($post)){
			$parts_list = array();
			foreach($value_post as $key => $val){
				if(preg_match("/^parts([0-9]+)_([a-z|_|0-9]+)$/",$key,$match)){
					if($match[2] == "main"){
						$parts_list[$match[1]][$match[2]] = $value_m_post[$key];
					}else {
						$parts_list[$match[1]][$match[2]] = $view_post[$key];
					}
				}
			}
		}
		if(is_array($_FILES)){
			foreach($_FILES as $key => $val){
				if(preg_match("/^parts([0-9]+)_([a-z|_|0-9]+)$/",$key,$match)){
					$parts_list[$match[1]][$match[2]] = $_FILES[$key];
				}
			}
		}
		if(is_array($parts_list)){
			$check_list = array();
			foreach($parts_list as $key => $val){
				if($parts_list[$key]['type'] != ""){
					$id_check = parts_id_check($parts_list,$post['id'],$cn);
					if($id_check == false){
						header("Location:".LIST_PAGE);
					}
					$form_check = "check_".$parts_list[$key]['type'];
					array_push($check_list,$form_check($parts_list[$key],$cn,$lang));
				}
			}
			if(is_array($check_list)){
				foreach($check_list as $key => $val){
					if($check_list[$key][1] == false){
						$check[1] = false;
						if(is_array($check_list[$key][0])){
							foreach($check_list[$key][0] as $key2 => $val2){
								array_push($check[0],$val2);
							}
						}
					}
				}
			}
		}
		if($check[1] == false){
			edit_form($Tpl,$cn,$lang,$select,$check,$post);
			exit;
		}

		$hidden = "";
		
		if(is_array($parts_list)){
			$parts = "";
			uasort($parts_list,"sort_new");
			$return_parts = view_confirm($parts_list,$post,$select,$lang);
			if($return_parts[2][1] == false){
				edit_form($Tpl,$cn,$lang,$select,$return_parts[2],$post);
				exit;
			}
			$Tpl->assign("parts",$return_parts[0]);
			$hidden .= $return_parts[1];
		}

		if($post != ""){
			foreach($view_post as $key => $val){
				if($key == "mon" || $key == "day"){
					$Tpl->assign($key,str_pad($val,2,0,STR_PAD_LEFT));
				}else {
					$Tpl->assign($key,$val);
				}
			}
			$Tpl->assign("date",$view_post['year']."/".str_pad($view_post['mon'],2,0,STR_PAD_LEFT)."/".str_pad($view_post['day'],2,0,STR_PAD_LEFT));
			foreach($value_post as $key => $val){
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
		//クエリチェック
		$check = form_check($post,$cn,$lang,$select);
		
		//パーツクエリチェック
		if(is_array($post)){
			$parts_list = array();
			foreach($post as $key => $val){
				if(preg_match("/^parts([0-9]+)_([a-z|_|0-9]+)$/",$key,$match)){
					$parts_list[$match[1]][$match[2]] = $post[$key];
				}
			}
		}
		if(is_array($parts_list)){
			$check_list = array();
			foreach($parts_list as $key => $val){
				if($parts_list[$key]['type'] != ""){
					$id_check = parts_id_check($parts_list,$post['id'],$cn);
					if($id_check == false){
						header("Location:".LIST_PAGE);
					}
					$form_check = "check_".$parts_list[$key]['type'];
					array_push($check_list,$form_check($parts_list[$key],$cn,$lang));
				}
			}
			if(is_array($check_list)){
				foreach($check_list as $key => $val){
					if($check_list[$key][1] == false){
						$check[1] = false;
						if(is_array($check_list[$key][0])){
							foreach($check_list[$key][0] as $key2 => $val2){
								array_push($check[0],$val2);
							}
						}
					}
				}
			}
		}

		if($check[1] == false){
			edit_form($Tpl,$cn,$lang,$select,$check,$post);
			exit;
		}
		
		//記事本体取得
		$sql = "select id,category,to_char(date,'yyyy') as year,file_name from news where id = ".$post['id'];
		$result = query_exec($cn,$sql);
		$news = result_assoc($result);
		
		//日付
		if($post['year'] != "" && $post['mon'] != "" && $post['day'] != ""){
			$date = $post['year']."-".(str_pad($post['mon'],2,0,STR_PAD_LEFT))."-".(str_pad($post['day'],2,0,STR_PAD_LEFT))." 00:00:00";
			$alt_date = substr($post['year'],2,2).(str_pad($post['mon'],2,0,STR_PAD_LEFT)).(str_pad($post['day'],2,0,STR_PAD_LEFT));
			$file_date = $post['year'].(str_pad($post['mon'],2,0,STR_PAD_LEFT)).(str_pad($post['day'],2,0,STR_PAD_LEFT));
		}
		//公開日
		if($post['open_date_year'] != "" && $post['open_date_mon'] != "" && $post['open_date_day'] != ""){
			$open_date = $post['open_date_year']."-".(str_pad($post['open_date_mon'],2,0,STR_PAD_LEFT))."-".(str_pad($post['open_date_day'],2,0,STR_PAD_LEFT))." ";
			if($post['open_date_hour'] != "" && $post['open_date_minute'] != ""){
				$open_date .= (str_pad($post['open_date_hour'],2,0,STR_PAD_LEFT)).":".(str_pad($post['open_date_minute'],2,0,STR_PAD_LEFT)).":00";
			}else {
				$open_date .= "00:00:00";
			}
		}
		//公開終了日
		if($post['close_date_year'] != "" && $post['close_date_mon'] != "" && $post['close_date_day'] != ""){
			$close_date = $post['close_date_year']."-".(str_pad($post['close_date_mon'],2,0,STR_PAD_LEFT))."-".(str_pad($post['close_date_day'],2,0,STR_PAD_LEFT))." ";
			if($post['close_date_hour'] != "" && $post['close_date_minute'] != ""){
				$close_date .= (str_pad($post['close_date_hour'],2,0,STR_PAD_LEFT)).":".(str_pad($post['close_date_minute'],2,0,STR_PAD_LEFT)).":00";
			}else {
				$close_date .= "00:00:00";
			}
		}
		
		$sql = "update news set ";
		$sql .= "lang = '".$lang."',";
		$sql .= "category = '".$select."',";
		$sql .= "user_id = '".$_SESSION['user_id']."',";
		$sql .= "date = '".$date."',";
		$sql .= "title = '".$post['title']."',";
		$sql .= "url = '".$post['url']."',";
		if($post['url_target'] == 1){
			$sql .= "url_target = 1,";
		}else {
			$sql .= "url_target = 0,";
		}
		$sql .= "detail_title = '".$post['detail_title']."',";
		if($open_date != ""){
			$sql .= "date_open = '".$open_date."',";
		}else {
			$sql .= "date_open = null,";
		}
		if($close_date != ""){
			$sql .= "date_close = '".$close_date."',";
		}else {
			$sql .= "date_close = null,";
		}
		if($post['open_flg'] == "1"){
			$sql .= "open_flg = 1,";
		}else {
			$sql .= "open_flg = 0,";
		}
		if($post['top_flg'] == "1"){
			$sql .= "top_flg = 1,";
		}else {
			$sql .= "top_flg = 0,";
		}
		//10.08.25 ニュースカテゴリ追加---
		if($post['news_category'] != ""){
			$sql .= "news_category = '".$post['news_category']."',";
		}
		//-----------------------
		$update_date = date("Y-m-d H:i:s");
		$sql .= "date_update = '".$update_date."'";
		$sql .= " where id = ".$post['id'];
		$result = query_exec($cn,$sql);
		if($result == true){
			$article_id = $post['id'];//記事ID
			//記事パーツ
			$sql = "select id,img_name,pdf_name from parts where article_id = ".$article_id." order by sort";
			$result = query_exec($cn,$sql);
			$parts = result_all($result);
			$parts_id = array();
			//パーツの登録
			if(is_array($parts_list)){
				uasort($parts_list,"sort_new");
				$img_cnt = 1;
				$pdf_cnt = 1;
				$sort = 1;
				foreach($parts_list as $key => $val){
					switch($parts_list[$key]['type']){
						case "main":
							if($parts_list[$key]['main'] != ""){
								if($parts_list[$key]['parts_id'] != ""){
									$sql = "update parts set ";
									$sql .= "sort = ".$sort.",";
									$sql .= "data_type = '".$parts_list[$key]['type']."',";
									$sql .= "main_text = '".$parts_list[$key]['main']."'";
									$sql .= " where ";
									$sql .= "article_id = ".$article_id." and ";
									$sql .= "id = ".$parts_list[$key]['parts_id'];
									query_exec($cn,$sql);
								}else {
									$sql = "insert into parts(";
									$sql .= "article_id,sort,data_type,main_text";
									$sql .= ") values (";
									$sql .= $article_id.",";
									$sql .= $sort.",";
									$sql .= "'".$parts_list[$key]['type']."',";
									$sql .= "'".$parts_list[$key]['main']."'";
									$sql .= ")";
									query_exec($cn,$sql);
								}
								$sort++;
							}
							break;
						case "caption":
							if($parts_list[$key]['caption'] != ""){
								if($parts_list[$key]['parts_id'] != ""){
									$sql = "update parts set ";
									$sql .= "sort = ".$sort.",";
									$sql .= "data_type = '".$parts_list[$key]['type']."',";
									$sql .= "caption = '".$parts_list[$key]['caption']."'";
									$sql .= " where ";
									$sql .= "article_id = ".$article_id." and ";
									$sql .= "id = ".$parts_list[$key]['parts_id'];
									query_exec($cn,$sql);
								}else {
									$sql = "insert into parts(";
									$sql .= "article_id,sort,data_type,caption";
									$sql .= ") values (";
									$sql .= $article_id.",";
									$sql .= $sort.",";
									$sql .= "'".$parts_list[$key]['type']."',";
									$sql .= "'".$parts_list[$key]['caption']."'";
									$sql .= ")";
									query_exec($cn,$sql);
								}
								$sort++;
							}
							break;
						case "img":
							if($parts_list[$key]['path'] != ""){
								if($parts_list[$key]['parts_id'] != ""){
									//前の画像を削除
									$sql = "select img_name from parts where id = ".$parts_list[$key]['parts_id'];
									$result = query_exec($cn,$sql);
									$img_name = result_assoc($result);
									temp_delete($img_name['img_name']);
								}
								$img_size = getimagesize($_SERVER['DOCUMENT_ROOT'].$parts_list[$key]['path']);
								//画像のアップ
								$img_path = img_move($file_date,$parts_list[$key]['path'],$parts_list[$key]['img_filename'],$select,$article_id,$lang,$img_cnt);
								$align = img_align($parts_list[$key]['img_align']);
								if($parts_list[$key]['img_alt'] != ""){
									$img_alt = $parts_list[$key]['img_alt'];
								}else {
									if($select == "news"){
										$img_alt = "news ".$alt_date." ".(str_pad($img_cnt,3,0,STR_PAD_LEFT));
									}else if($select == "ir"){
										$img_alt = "IR news ".$alt_date." ".(str_pad($img_cnt,3,0,STR_PAD_LEFT));
									}else {
										$img_alt = "news ".$alt_date." ".(str_pad($img_cnt,3,0,STR_PAD_LEFT));
									}
								}
								if($parts_list[$key]['parts_id'] != ""){
									$sql = "update parts set ";
									$sql .= "sort = ".$sort.",";
									$sql .= "data_type = '".$parts_list[$key]['type']."',";
									$sql .= "img_name = '".$img_path."',";
									$sql .= "img_align = '".$align."',";
									$sql .= "img_width = ".$img_size[0].",";
									$sql .= "img_height = ".$img_size[1].",";
									$sql .= "img_alt = '".$img_alt."'";
									$sql .= " where ";
									$sql .= "article_id = ".$article_id." and ";
									$sql .= "id = ".$parts_list[$key]['parts_id'];
									query_exec($cn,$sql);
								}else {
									$sql = "insert into parts(";
									$sql .= "article_id,sort,data_type,";
									$sql .= "img_name,";
									$sql .= "img_align,";
									$sql .= "img_width,";
									$sql .= "img_height,";
									$sql .= "img_alt";
									$sql .= ") values (";
									$sql .= $article_id.",";
									$sql .= $sort.",";
									$sql .= "'".$parts_list[$key]['type']."',";
									$sql .= "'".$img_path."',";
									$sql .= "'".$align."',";
									$sql .= $img_size[0].",";
									$sql .= $img_size[1].",";
									$sql .= "'".$img_alt."'";
									$sql .= ")";
									query_exec($cn,$sql);
								}
								$img_cnt++;
								$sort++;
							}else if($parts_list[$key]['img_name'] != "") {
								$align = img_align($parts_list[$key]['img_align']);
								if($parts_list[$key]['img_alt'] != ""){
									$img_alt = $parts_list[$key]['img_alt'];
								}else {
									if($select == "news"){
										$img_alt = "news ".$alt_date." ".(str_pad($img_cnt,3,0,STR_PAD_LEFT));
									}else if($select == "ir"){
										$img_alt = "IR news ".$alt_date." ".(str_pad($img_cnt,3,0,STR_PAD_LEFT));
									}else {
										$img_alt = "news ".$alt_date." ".(str_pad($img_cnt,3,0,STR_PAD_LEFT));
									}
								}
								if($parts_list[$key]['parts_id'] != ""){
									//ファイル名の取り出し
									if($parts_list[$key]['img_filename'] == ""){
										$file_name = basename($parts_list[$key]['img_name']);
										preg_match("/^[0-9]+_[0-9]+_(.+)$/",$file_name,$match);
										$img_filename = $match[1];
									}else {
										$img_filename = $parts_list[$key]['img_filename'];
									}
									//前の画像を取得
									$sql = "select img_name from parts where id = ".$parts_list[$key]['parts_id'];
									$result = query_exec($cn,$sql);
									$img_name = result_assoc($result);
									if($img_name['img_name'] != $parts_list[$key]['img_name']){
									//前の画像を削除
										temp_delete($img_name['img_name']);
									}
									//画像のアップ
									$img_path = img_move($file_date,$parts_list[$key]['img_name'],$img_filename,$select,$article_id,$lang,$img_cnt);
									$sql = "update parts set ";
									$sql .= "sort = ".$sort.",";
									$sql .= "data_type = '".$parts_list[$key]['type']."',";
									$sql .= "img_name = '".$img_path."',";
									$sql .= "img_align = '".$align."',";
									$sql .= "img_width = ".$parts_list[$key]['img_width'].",";
									$sql .= "img_height = ".$parts_list[$key]['img_height'].",";
									$sql .= "img_alt = '".$img_alt."'";
									$sql .= " where ";
									$sql .= "article_id = ".$article_id." and ";
									$sql .= "id = ".$parts_list[$key]['parts_id'];
									query_exec($cn,$sql);
								}else {
									$img_size = getimagesize($_SERVER['DOCUMENT_ROOT'].$parts_list[$key]['img_name']);
									//画像のアップ
									$img_path = img_move($file_date,$parts_list[$key]['img_name'],$parts_list[$key]['img_filename'],$select,$article_id,$lang,$img_cnt);
									$sql = "insert into parts(";
									$sql .= "article_id,sort,data_type,";
									$sql .= "img_name,";
									$sql .= "img_align,";
									$sql .= "img_width,";
									$sql .= "img_height,";
									$sql .= "img_alt";
									$sql .= ") values (";
									$sql .= $article_id.",";
									$sql .= $sort.",";
									$sql .= "'".$parts_list[$key]['type']."',";
									$sql .= "'".$img_path."',";
									$sql .= "'".$align."',";
									$sql .= $img_size[0].",";
									$sql .= $img_size[1].",";
									$sql .= "'".$img_alt."'";
									$sql .= ")";
									query_exec($cn,$sql);
								}
								$img_cnt++;
								$sort++;
							}
							break;
						case "link":
							$block_id = $sort;
							for($i=1;$i<=PARTS_LINK_MAX;$i++){
								if($parts_list[$key]['link_text'.$i] != "" && $parts_list[$key]['link_url'.$i] != ""){
									if($parts_list[$key]['parts_id'.$i] != ""){
										$sql = "update parts set ";
										$sql .= "sort = ".$sort.",";
										$sql .= "data_type = '".$parts_list[$key]['type']."',";
										$sql .= "link_text = '".$parts_list[$key]['link_text'.$i]."',";
										$sql .= "link_url = '".$parts_list[$key]['link_url'.$i]."',";
										if($parts_list[$key]['link_target'.$i] == 1){
											$sql .= "link_target = 1,";
										}else {
											$sql .= "link_target = 0,";
										}
										$sql .= "block_id = ".$block_id;
										$sql .= " where ";
										$sql .= "article_id = ".$article_id." and ";
										$sql .= "id = ".$parts_list[$key]['parts_id'.$i];
										query_exec($cn,$sql);
									}else {
										$sql = "insert into parts(";
										$sql .= "article_id,sort,data_type,link_text,link_url,link_target,block_id";
										$sql .= ") values (";
										$sql .= $article_id.",";
										$sql .= $sort.",";
										$sql .= "'".$parts_list[$key]['type']."',";
										$sql .= "'".$parts_list[$key]['link_text'.$i]."',";
										$sql .= "'".$parts_list[$key]['link_url'.$i]."',";
										if($parts_list[$key]['link_target'.$i] == 1){
											$sql .= "1,";
										}else {
											$sql .= "0,";
										}
										$sql .= $block_id;
										$sql .= ")";
										query_exec($cn,$sql);
									}
									$sort++;
								}else if($parts_list[$key]['parts_id'.$i] != "" && $parts_list[$key]['link_text'.$i] == ""){
									$sql = "delete from parts where id = ".$parts_list[$key]['parts_id'.$i];
									query_exec($cn,$sql);
								}
							}
							break;
						case "pdf":
							$block_id = $sort;
							for($i=1;$i<=PARTS_PDF_MAX;$i++){
								if($parts_list[$key]['pdf_text'.$i] != "" && $parts_list[$key]['path'.$i] != ""){
									if($parts_list[$key]['parts_id'.$i] != ""){
										//前のPDFを削除
										$sql = "select pdf_name from parts where id = ".$parts_list[$key]['parts_id'.$i];
										$result = query_exec($cn,$sql);
										$pdf_name = result_assoc($result);
										temp_delete($pdf_name['pdf_name']);
									}
									//PDFのアップ
									$pdf_path = pdf_move($file_date,$parts_list[$key]['path'.$i],$parts_list[$key]['pdf_filename'.$i],$select,$article_id,$lang,$pdf_cnt);
									if($parts_list[$key]['parts_id'.$i] != ""){
										$sql = "update parts set ";
										$sql .= "sort = ".$sort.",";
										$sql .= "data_type = '".$parts_list[$key]['type']."',";
										$sql .= "pdf_text = '".$parts_list[$key]['pdf_text'.$i]."',";
										$sql .= "pdf_name = '".$pdf_path."',";
										$sql .= "pdf_size = ".$parts_list[$key]['pdf_size'.$i].",";
										$sql .= "block_id = ".$block_id;
										$sql .= " where ";
										$sql .= "article_id = ".$article_id." and ";
										$sql .= "id = ".$parts_list[$key]['parts_id'.$i];
										query_exec($cn,$sql);
									}else {
										$sql = "insert into parts(";
										$sql .= "article_id,sort,data_type,pdf_text,pdf_name,pdf_size,block_id";
										$sql .= ") values (";
										$sql .= $article_id.",";
										$sql .= $sort.",";
										$sql .= "'".$parts_list[$key]['type']."',";
										$sql .= "'".$parts_list[$key]['pdf_text'.$i]."',";
										$sql .= "'".$pdf_path."',";
										$sql .= $parts_list[$key]['pdf_size'.$i].",";
										$sql .= $block_id;
										$sql .= ")";
										query_exec($cn,$sql);
									}
									$pdf_cnt++;
									$sort++;
								}else if($parts_list[$key]['pdf_text'.$i] != "" && $parts_list[$key]['parts_id'.$i] != "" && $parts_list[$key]['pdf_name'.$i] != ""){
									//ファイル名の取り出し
									if($parts_list[$key]['pdf_filename'.$i] == ""){
										$file_name = basename($parts_list[$key]['pdf_name'.$i]);
										preg_match("/^[0-9]+_[0-9]+_(.+)$/",$file_name,$match);
										$pdf_filename = $match[1];
									}else {
										$pdf_filename = $parts_list[$key]['pdf_filename'.$i];
									}
									//前のPDFを取得
									$sql = "select pdf_name from parts where id = ".$parts_list[$key]['parts_id'.$i];
									$result = query_exec($cn,$sql);
									$pdf_name = result_assoc($result);
									if($pdf_name['pdf_name'] != $parts_list[$key]['pdf_name'.$i]){
										//前のPDFを削除
										temp_delete($pdf_name['pdf_name']);
									}
									//PDFのアップ
									$pdf_path = pdf_move($file_date,$parts_list[$key]['pdf_name'.$i],$pdf_filename,$select,$article_id,$lang,$pdf_cnt);
									$sql = "update parts set ";
									$sql .= "sort = ".$sort.",";
									$sql .= "data_type = '".$parts_list[$key]['type']."',";
									$sql .= "pdf_text = '".$parts_list[$key]['pdf_text'.$i]."',";
									$sql .= "pdf_name = '".$pdf_path."',";
									$sql .= "pdf_size = ".$parts_list[$key]['pdf_size'.$i].",";
									$sql .= "block_id = ".$block_id;
									$sql .= " where ";
									$sql .= "article_id = ".$article_id." and ";
									$sql .= "id = ".$parts_list[$key]['parts_id'.$i];
									query_exec($cn,$sql);
									$pdf_cnt++;
									$sort++;
								}else if($parts_list[$key]['parts_id'.$i] != "" && $parts_list[$key]['pdf_text'.$i] == ""){
									if($parts_list[$key]['pdf_name'.$i] != ""){
										temp_delete($parts_list[$key]['pdf_name'.$i]);
									}
									$sql = "delete from parts where id = ".$parts_list[$key]['parts_id'.$i];
									query_exec($cn,$sql);
								}
							}
							break;
					}
					if(is_array($parts)){
						foreach($parts as $key2 => $val2){
							$parts_id[$key2]['id'] = $parts[$key2]['id'];
							$parts_id[$key2]['img_name'] = $parts[$key2]['img_name'];
							$parts_id[$key2]['pdf_name'] = $parts[$key2]['pdf_name'];
							if($parts_list[$key]['type'] != "link" && $parts_list[$key]['type'] != "pdf"){
								if($parts[$key2]['id'] == $parts_list[$key]['parts_id']){
									$parts_id[$key2]['flg'] = 1;
								}else {
									if($parts_id[$key2]['flg'] != 1){
										$parts_id[$key2]['flg'] = 0;
									}
								}
							}else {
								if($parts_list[$key]['type'] == "link"){
									for($i=1;$i<=PARTS_LINK_MAX;$i++){
										if($parts[$key2]['id'] == $parts_list[$key]['parts_id'.$i]){
											$parts_id[$key2]['flg'] = 1;
										}else {
											if($parts_id[$key2]['flg'] != 1){
												$parts_id[$key2]['flg'] = 0;
											}
										}
									}
								}else if($parts_list[$key]['type'] == "pdf") {
									for($i=1;$i<=PARTS_PDF_MAX;$i++){
										if($parts[$key2]['id'] == $parts_list[$key]['parts_id'.$i]){
											$parts_id[$key2]['flg'] = 1;
										}else {
											if($parts_id[$key2]['flg'] != 1){
												$parts_id[$key2]['flg'] = 0;
											}
										}
									}
								}
							}
						}
					}
				}
			}
			//不要なパーツの削除
			if(is_array($parts_id)){
				foreach($parts_id as $key => $val){
					if($parts_id[$key]['flg'] == 0){
						if($parts_id[$key]['img_name'] != ""){
							temp_delete($parts_id[$key]['img_name']);
						}
						if($parts_id[$key]['pdf_name'] != ""){
							temp_delete($parts_id[$key]['pdf_name']);
						}
						
						$sql = "delete from parts where id = ".$parts_id[$key]['id'];
						query_exec($cn,$sql);
					}
				}
			}
			
			//編集前の記事を削除する
			delete_html($news['file_name'],$article_id,$cn);
			//HTMLの即時反映
			if($post['open_date_year'] != "" && $post['open_date_mon'] != "" && $post['open_date_day'] != "" && $post['open_date_hour'] != "" && $post['open_date_minute'] != ""){
				$open_date_a = $post['open_date_year'].(str_pad($post['open_date_mon'],2,0,STR_PAD_LEFT)).(str_pad($post['open_date_day'],2,0,STR_PAD_LEFT)).(str_pad($post['open_date_hour'],2,0,STR_PAD_LEFT)).(str_pad($post['open_date_minute'],2,0,STR_PAD_LEFT))."00";
			}
			if($post['close_date_year'] != "" && $post['close_date_mon'] != "" && $post['close_date_day'] != "" && $post['close_date_hour'] != "" && $post['close_date_minute'] != ""){
				$close_date_a = $post['close_date_year'].(str_pad($post['close_date_mon'],2,0,STR_PAD_LEFT)).(str_pad($post['close_date_day'],2,0,STR_PAD_LEFT)).(str_pad($post['close_date_hour'],2,0,STR_PAD_LEFT)).(str_pad($post['close_date_minute'],2,0,STR_PAD_LEFT))."00";
			}
			$now = get_now($lang);
			if($post['url'] == "" && $post['open_flg'] == "1" && ($open_date_a == "" || $open_date_a <= $now) && ($close_date_a == "" || $close_date_a > $now)){
				create_html($cn,$article_id,$Tpl);
			}
			restructure_del_html($cn,$lang,$news['category'],$news['year'],$Tpl);
			restructure_html($cn,$article_id,$Tpl);
			create_list($cn,$lang,$select,$Tpl);
			create_xml($cn,$lang,$Tpl);
			create_rss($cn,$lang,$Tpl);
		}
		header("Location:".LIST_PAGE."?year=".$news['year']);
	}

?>