<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	require_once("./auth.php");

	
	//入力チェック
	function form_check($post,$cn,$lang,$select=null){
		$error_flg = true;
		$error_msg = array();
		
		/*----------日付----------*/
		//年
		$y_error_flg = true;
		if($post['year'] != ""){
			if(!num_check($post['year'])){
				$error_flg = false;
				$y_error_flg = false;
				array_push($error_msg,"The input of year in \"Date\" is wrong.");
			}else {
				$min = 1990;
				$max = 2100;
				if($post['year'] < $min || $post['year'] > $max){
					$error_flg = false;
					$y_error_flg = false;
					array_push($error_msg,"The input of year in \"Date\" is wrong.");
				}
			}
		}else {
			$error_flg = false;
			$y_error_flg = false;
			array_push($error_msg,"Please input year in \"Date\".");
		}
		//月
		$m_error_flg = true;
		if($post['mon'] != ""){
			if(!num_check($post['mon'])){
				$error_flg = false;
				$m_error_flg = false;
				array_push($error_msg,"The input of month in \"Date\" is wrong.");
			}else {
				$min = 1;
				$max = 12;
				if($post['mon'] < $min || $post['mon'] > $max){
					$error_flg = false;
					$m_error_flg = false;
					array_push($error_msg,"The input of month in \"Date\" is wrong.");
				}
			}
		}else {
			$error_flg = false;
			$m_error_flg = false;
			array_push($error_msg,"Please input month in \"Date\".");
		}
		//日
		$d_error_flg = true;
		if($post['day'] != ""){
			if(!num_check($post['day'])){
				$error_flg = false;
				$d_error_flg = false;
				array_push($error_msg,"The input of day in \"Date\" is wrong.");
			}else {
				$min = 1;
				$max = 31;
				if($post['day'] < $min || $post['day'] > $max){
					$error_flg = false;
					$d_error_flg = false;
					array_push($error_msg,"The input of day in \"Date\" is wrong.");
				}
			}
		}else {
			$error_flg = false;
			$d_error_flg = false;
			array_push($error_msg,"Please input day in \"Date\".");
		}
		//日付
		if($post['year'] != "" && $post['mon'] != "" && $post['day'] != ""){
			if($y_error_flg == true && $m_error_flg == true && $d_error_flg == true){
				if(!date_check($post['year'],$post['mon'],$post['day'])){
					$error_flg = false;
					array_push($error_msg,"The input of \"Date\" is wrong.");
				}
			}
		}

		/*----------タイトル----------*/
		if($post['title'] == ""){
			$error_flg = false;
			array_push($error_msg,"Please input \"Title\".");
		}

		/*----------URL----------*/
		//url
		if($post['url'] != ""){
			if(!url_check($post['url'])){
				$error_flg = false;
				array_push($error_msg,"The input of \"Hyperlink\" is wrong.");
			}
		}
		//別窓
		if($post['url_target'] != ""){
			if($post['url_target'] != 1){
				$error_flg = false;
				array_push($error_msg,"The proper URL must be specified before selecting \"Open in new window\".");
			}
		}
		
		//10.08.26 追加
		/*----------カテゴリ----------*/
		if($select == "news"){
			if($post['news_category'] == ""){
//				$error_flg = false;
//				array_push($error_msg,"Please input \"News Category\".");
			}else {
				if(!num_check($post['news_category'])){
					$error_flg = false;
					array_push($error_msg,"The input of \"News Category\" is wrong.");
				}else {
					$sql = "select count(*) from news_category where id = ".$post['news_category']."";
					$result = query_exec($cn,$sql);
					$ncategory = result_assoc($result);
					if($ncategory['count'] <= 0){
						$error_flg = false;
						array_push($error_msg,"The input of \"News Category\" is wrong.");
					}
				}
			}
		}

		
		/*----------公開日----------*/
		//年
		$y_error_flg = true;
		if($post['open_date_year'] != ""){
			if(!num_check($post['open_date_year'])){
				$error_flg = false;
				$y_error_flg = false;
				array_push($error_msg,"The input of year in \"Start Publishing\" is wrong.");
			}else {
				$min = 1990;
				$max = 2100;
				if($post['open_date_year'] < $min || $post['open_date_year'] > $max){
					$error_flg = false;
					$y_error_flg = false;
					array_push($error_msg,"The input of year in \"Start Publishing\" is wrong.");
				}
			}
		}
		//月
		$m_error_flg = true;
		if($post['open_date_mon'] != ""){
			if(!num_check($post['open_date_mon'])){
				$error_flg = false;
				$m_error_flg = false;
				array_push($error_msg,"The input of month in \"Start Publishing\" is wrong.");
			}else {
				$min = 1;
				$max = 12;
				if($post['open_date_mon'] < $min || $post['open_date_mon'] > $max){
					$error_flg = false;
					$m_error_flg = false;
					array_push($error_msg,"The input of month in \"Start Publishing\" is wrong.");
				}
			}
		}
		//日
		$d_error_flg = true;
		if($post['open_date_day'] != ""){
			if(!num_check($post['open_date_day'])){
				$error_flg = false;
				$d_error_flg = false;
				array_push($error_msg,"The input of day in \"Start Publishing\" is wrong.");
			}else {
				$min = 1;
				$max = 31;
				if($post['open_date_day'] < $min || $post['open_date_day'] > $max){
					$error_flg = false;
					$d_error_flg = false;
					array_push($error_msg,"The input of day in \"Start Publishing\" is wrong.");
				}
			}
		}
		//日付
		if($post['open_date_year'] != "" && $post['open_date_mon'] != "" && $post['open_date_day'] != ""){
			if($y_error_flg == true && $m_error_flg == true && $d_error_flg == true){
				if(!date_check($post['open_date_year'],$post['open_date_mon'],$post['open_date_day'])){
					$error_flg = false;
					array_push($error_msg,"The input of \"Start Publishing\" is wrong.");
				}
			}
		}
		//時間
		$h_error_flg = true;
		if($post['open_date_hour'] != ""){
			if(!num_check($post['open_date_hour'])){
				$error_flg = false;
				$h_error_flg = false;
				array_push($error_msg,"The input of hours in \"Start Publishing\" is wrong.");
			}else {
				$min = 00;
				$max = 24;
				if($post['open_date_hour'] < $min || $post['open_date_hour'] > $max){
					$error_flg = false;
					$h_error_flg = false;
					array_push($error_msg,"The input of hours in \"Start Publishing\" is wrong.");
				}
			}
		}
		//分
		$n_error_flg = true;
		if($post['open_date_minute'] != ""){
			if(!num_check($post['open_date_minute'])){
				$error_flg = false;
				$n_error_flg = false;
				array_push($error_msg,"The input of minutes in \"Start Publishing\" is wrong.");
			}else {
				$min = 00;
				$max = 59;
				if($post['open_date_minute'] < $min || $post['open_date_minute'] > $max){
					$error_flg = false;
					$n_error_flg = false;
					array_push($error_msg,"The input of minutes in \"Start Publishing\" is wrong.");
				}
			}
		}
		if($post['open_date_year'] != "" && $post['open_date_mon'] != "" && $post['open_date_day'] != "" && $post['open_date_hour'] != "" && $post['open_date_minute'] != ""){
			if($y_error_flg == true && $m_error_flg == true && $d_error_flg == true && $h_error_flg == true && $n_error_flg == true){
				$open_date = $post['open_date_year'].(str_pad($post['open_date_mon'],2,0,STR_PAD_LEFT)).(str_pad($post['open_date_day'],2,0,STR_PAD_LEFT)).(str_pad($post['open_date_hour'],2,0,STR_PAD_LEFT)).(str_pad($post['open_date_minute'],2,0,STR_PAD_LEFT));
			}
		}
		
		/*----------公開終了日----------*/
		//年
		$y_error_flg = true;
		if($post['close_date_year'] != ""){
			if(!num_check($post['close_date_year'])){
				$error_flg = false;
				$y_error_flg = false;
				array_push($error_msg,"The input of year in \"Stop Publishing\" is wrong.");
			}else {
				$min = 1990;
				$max = 2100;
				if($post['close_date_year'] < $min || $post['close_date_year'] > $max){
					$error_flg = false;
					$y_error_flg = false;
					array_push($error_msg,"The input of year in \"Stop Publishing\" is wrong.");
				}
			}
		}
		//月
		$m_error_flg = true;
		if($post['close_date_mon'] != ""){
			if(!num_check($post['close_date_mon'])){
				$error_flg = false;
				$m_error_flg = false;
				array_push($error_msg,"The input of month in \"Stop Publishing\" is wrong.");
			}else {
				$min = 1;
				$max = 12;
				if($post['close_date_mon'] < $min || $post['close_date_mon'] > $max){
					$error_flg = false;
					$m_error_flg = false;
					array_push($error_msg,"The input of month in \"Stop Publishing\" is wrong.");
				}
			}
		}
		//日
		$d_error_flg = true;
		if($post['close_date_day'] != ""){
			if(!num_check($post['close_date_day'])){
				$error_flg = false;
				$d_error_flg = false;
				array_push($error_msg,"The input of day in \"Stop Publishing\" is wrong.");
			}else {
				$min = 1;
				$max = 31;
				if($post['close_date_day'] < $min || $post['close_date_day'] > $max){
					$error_flg = false;
					$d_error_flg = false;
					array_push($error_msg,"The input of day in \"Stop Publishing\" is wrong.");
				}
			}
		}
		//日付
		if($post['close_date_year'] != "" && $post['close_date_mon'] != "" && $post['close_date_day'] != ""){
			if($y_error_flg == true && $m_error_flg == true && $d_error_flg == true){
				if(!date_check($post['close_date_year'],$post['close_date_mon'],$post['close_date_day'])){
					$error_flg = false;
					array_push($error_msg,"The input of \"Stop Publishing\" is wrong.");
				}
			}
		}
		//時間
		$h_error_flg = true;
		if($post['close_date_hour'] != ""){
			if(!num_check($post['close_date_hour'])){
				$error_flg = false;
				$h_error_flg = false;
				array_push($error_msg,"The input of hours in \"Stop Publishing\" is wrong.");
			}else {
				$min = 00;
				$max = 24;
				if($post['close_date_hour'] < $min || $post['close_date_hour'] > $max){
					$error_flg = false;
					$h_error_flg = false;
					array_push($error_msg,"The input of hours in \"Stop Publishing\" is wrong.");
				}
			}
		}
		//分
		$n_error_flg = true;
		if($post['close_date_minute'] != ""){
			if(!num_check($post['close_date_minute'])){
				$error_flg = false;
				$n_error_flg = false;
				array_push($error_msg,"The input of minutes in \"Stop Publishing\" is wrong.");
			}else {
				$min = 00;
				$max = 59;
				if($post['close_date_minute'] < $min || $post['close_date_minute'] > $max){
					$error_flg = false;
					$n_error_flg = false;
					array_push($error_msg,"The input of minutes in \"Stop Publishing\" is wrong.");
				}
			}
		}
		if($post['close_date_year'] != "" && $post['close_date_mon'] != "" && $post['close_date_day'] != "" && $post['close_date_hour'] != "" && $post['close_date_minute'] != ""){
			if($y_error_flg == true && $m_error_flg == true && $d_error_flg == true && $h_error_flg == true && $n_error_flg == true){
				$close_date = $post['close_date_year'].(str_pad($post['close_date_mon'],2,0,STR_PAD_LEFT)).(str_pad($post['close_date_day'],2,0,STR_PAD_LEFT)).(str_pad($post['close_date_hour'],2,0,STR_PAD_LEFT)).(str_pad($post['close_date_minute'],2,0,STR_PAD_LEFT));
			}
		}
		
		/*----------公開終了日のチェック----------*/
		if($open_date != "" && $close_date != ""){
			if($open_date >= $close_date){
				$error_flg = false;
				array_push($error_msg,"The date specified in \"Stop Publishing\" is before the date specified in \"Start Publishing\".");
			}
		}

		/*----------公開フラグ----------*/
		if($post['open_flg'] != ""){
			if($post['open_flg'] != 1 && $post['open_flg'] != 2){
				$error_flg = false;
				array_push($error_msg,"The publishing date must be specified before selecting \"Status\".");
			}
		}
		/*----------トップフラグ----------*/
		if($post['top_flg'] != ""){
			if($post['top_flg'] != 1 && $post['top_flg'] != 2){
				$error_flg = false;
				array_push($error_msg,"Unexpected error has occurred during the process.");
			}
		}
		
		
		
		return(array($error_msg,$error_flg));
	}
	
	//本文パーツチェック
	function check_main($post,$cn){
		$error_flg = true;
		$error_msg = array();
		return(array($error_msg,$error_flg));
	}
	//小見出しパーツチェック
	function check_caption($post,$cn){
		$error_flg = true;
		$error_msg = array();
		if($post['caption'] != ""){
			if(!limit_admin_check($post['caption'],HEADER_MAX)){//小見出しの文字数制限
				$error_flg = false;
				array_push($error_msg,"Please input the Header within ".HEADER_MAX." characters.");
			}
		}
		return(array($error_msg,$error_flg));
	}
	//画像パーツチェック
	function check_img($post,$cn){
		$error_flg = true;
		$error_msg = array();
		if($post['img'] != "" && $post['img']['error'] == 0){
			if(!eisu_check($post['img']['name'])){
				$error_flg = false;
				array_push($error_msg,"Please input the name of the images file in the alphanumeric characters.");
			}else {
				if(!preg_match("/^.+\.gif$/",$post['img']['name']) && !preg_match("/^.+\.jpg$/",$post['img']['name']) && !preg_match("/^.+\.png$/",$post['img']['name'])){
					$error_flg = false;
					array_push($error_msg,"The file type of this image file is not supported. (Only jpg, gif, png)");
				}
			}
		}
		if($post['img_align'] != ""){
			if($post['img_align'] != "1" && $post['img_align'] != "2" && $post['img_align'] != "3"){
				$error_flg = false;
				array_push($error_msg,"The file must be specified before selecting alignment of image.");
			}
		}
		return(array($error_msg,$error_flg));
	}
	//リンクパーツチェック
	function check_link($post,$cn){
		$error_flg = true;
		$error_msg = array();
		for($i=1;$i<=PARTS_LINK_MAX;$i++){
			//url
			if($post['link_url'.$i] != ""){
				if(!url_check($post['link_url'.$i])){
					$error_flg = false;
					array_push($error_msg,"The input of \"Link".$i."\" is wrong.");
				}
			}
			//別窓
			if($post['link_target'.$i] != ""){
				if($post['link_target'.$i] != 1){
					$error_flg = false;
					array_push($error_msg,"The proper URL for \"Hyperlink URL".$i."\" must be specified before selecting \"Open in new window\".");
				}
			}
		}
		return(array($error_msg,$error_flg));
	}
	//PDFパーツチェック
	function check_pdf($post,$cn){
		$error_flg = true;
		$error_msg = array();
		for($i=1;$i<=PARTS_PDF_MAX;$i++){
			if($post['pdf_file'.$i] != "" && $post['pdf_file'.$i]['error'] == 0){
				if(!eisu_check($post['pdf_file'.$i]['name'])){
					$error_flg = false;
					array_push($error_msg,"Please input the name of PDF files in alphanumeric characters.");
				}else {
					if(!preg_match("/^.+\.pdf$/",$post['pdf_file'.$i]['name'])){
						$error_flg = false;
						array_push($error_msg,"The file type of \"PDF link".$i."\" is not supported.");
					}
				}
			}
		}
		return(array($error_msg,$error_flg));
	}
	
	//画像位置
	function img_align($align){
		switch($align){
			case 1 :
				$img_align = "left";
				break;
			case 2 :
				$img_align = "center";
				break;
			case 3 :
				$img_align = "right";
				break;
			default : 
				$img_align = "left";
				break;
		}
		return($img_align);
	}
	//画像位置逆変換
	function img_align_tonum($align){
		switch($align){
			case "left" :
				$img_align = 1;
				break;
			case "center" :
				$img_align = 2;
				break;
			case "right" :
				$img_align = 3;
				break;
			default : 
				$img_align = 1;
				break;
		}
		return($img_align);
	}
	
	//重複チェック
	function check_repeat($post,$cn,$lang,$select,$user){
		$error_flg = true;
		
		//日付
		if($post['year'] != "" && $post['mon'] != "" && $post['day'] != ""){
			$date = $post['year']."-".(str_pad($post['mon'],2,0,STR_PAD_LEFT))."-".(str_pad($post['day'],2,0,STR_PAD_LEFT))." 00:00:00";
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

		$sql = "select id from news ";
		$sql .= "where ";
		$sql .= "lang = '".$lang."' and ";
		$sql .= "category = '".$select."' and ";
		$sql .= "user_id = '".$user."' and ";
		$sql .= "date = '".$date."' and ";
		$sql .= "title = '".$post['title']."' and ";
		if($post['url'] != ""){
			$sql .= "url = '".$post['url']."' and ";
		}
		if($post['url_target'] != ""){
			$sql .= "url_target = '".$post['url_target']."' and ";
		}
		if($post['detail_title'] != ""){
			$sql .= "detail_title = '".$post['detail_title']."' and ";
		}
		if($open_date != ""){
			$sql .= "date_open = '".$open_date."' and ";
		}
		if($close_date != ""){
			$sql .= "date_close = '".$close_date."' and ";
		}
		if($post['open_flg'] == 1){
			$sql .= "open_flg = 1 and ";
		}else {
			$sql .= "open_flg = 0 and ";
		}
		if($post['top_flg'] == 1){
			$sql .= "top_flg = 1";
		}else {
			$sql .= "top_flg = 0";
		}
		$result = query_exec($cn,$sql);
		$count = result_all($result);
		if($count != ""){
			$error_flg = false;
		
			//パーツのチェック
			foreach($post as $key => $val){
				if(preg_match("/^parts([0-9]+)_([a-z|_|0-9]+)$/",$key,$match)){
					if($match[2] == "main"){
						$parts_list[$match[1]][$match[2]] = $post[$key];
					}else {
						$parts_list[$match[1]][$match[2]] = $post[$key];
					}
				}
			}
			if(is_array($parts_list) && is_array($count)){
				foreach($parts_list as $key => $val){
					switch($parts_list[$key]['type']){
						case "main" :
							foreach($count as $id_key => $id_val){
								$sql = "select count(*) from parts where data_type = 'main' and main_text = '".$parts_list[$key]['main']."' and article_id = ".$count[$id_key]['id'];
								$result = query_exec($cn,$sql);
								$main = result_assoc($result);
								if($main['count'] > 0){
									$error_flg = false;
								}else {
									$error_flg = true;
								}
							}
							break;
						case "caption" :
							foreach($count as $id_key => $id_val){
								$sql = "select count(*) from parts where data_type = 'caption' and caption = '".$parts_list[$key]['caption']."' and article_id = ".$count[$id_key]['id'];
								$result = query_exec($cn,$sql);
								$caption = result_assoc($result);
								if($caption['count'] > 0){
									$error_flg = false;
								}else {
									$error_flg = true;
								}
							}
							break;
						case "img" :
							foreach($count as $id_key => $id_val){
								if($parts_list[$key]['path'] != "" && file_exists($_SERVER['DOCUMENT_ROOT'].$parts_list[$key]['path'])){
									$img_size = getimagesize($_SERVER['DOCUMENT_ROOT'].$parts_list[$key]['path']);
									$sql = "select count(*) from parts where data_type = 'img' and img_name = '".$parts_list[$key]['path']."' and img_align = '".$parts_list[$key]['img_align']."' and img_width = '".$img_size[0]."' and img_height = '".$img_size[1]."' and img_alt = '".$parts_list[$key]['img_alt']."' and article_id = ".$count[$id_key]['id'];
									$result = query_exec($cn,$sql);
									$img = result_assoc($result);
									if($img['count'] > 0){
										$error_flg = false;
									}else {
										$error_flg = true;
									}
								}else if($parts_list[$key]['img_name'] != "" && file_exists($_SERVER['DOCUMENT_ROOT'].$parts_list[$key]['img_name'])){
									$img_size = getimagesize($_SERVER['DOCUMENT_ROOT'].$parts_list[$key]['img_name']);
									$sql = "select count(*) from parts where data_type = 'img' and img_name = '".$parts_list[$key]['img_name']."' and img_align = '".$parts_list[$key]['img_align']."' and img_width = '".$img_size[0]."' and img_height = '".$img_size[1]."' and img_alt = '".$parts_list[$key]['img_alt']."' and article_id = ".$count[$id_key]['id'];
									$result = query_exec($cn,$sql);
									$img = result_assoc($result);
									if($img['count'] > 0){
										$error_flg = false;
									}else {
										$error_flg = true;
									}
								}
							}
							break;
						case "link" :
							for($i=1;$i<=PARTS_LINK_MAX;$i++){
								foreach($count as $id_key => $id_val){
									$sql = "select count(*) from parts where data_type = 'link' and link_text = '".$parts_list[$key]['link_text'.$i]."' and link_url = '".$parts_list[$key]['link_url'.$i]."'";
									if($parts_list[$key]['link_target'.$i] != ""){
										$sql .= " and link_target = '".$parts_list[$key]['link_target'.$i]."'";
									}
									$sql .= " and article_id = ".$count[$id_key]['id'];
									$result = query_exec($cn,$sql);
									$link = result_assoc($result);
									if($link['count'] > 0){
										$error_flg = false;
									}else {
										$error_flg = true;
									}
								}
							}
							break;
						case "pdf" :
							for($i=1;$i<=PARTS_PDF_MAX;$i++){
								foreach($count as $id_key => $id_val){
									if($parts_list[$key]['path'.$i] != ""){
										$sql = "select count(*) from parts where data_type = 'pdf' and pdf_text = '".$parts_list[$key]['pdf_text'.$i]."' and pdf_name = '".$parts_list[$key]['path'.$i]."' and pdf_size = '".$parts_list[$key]['pdf_size'.$i]."' and article_id = ".$count[$id_key]['id'];
									}else if($parts_list[$key]['pdf_name'.$i] != ""){
										$sql = "select count(*) from parts where data_type = 'pdf' and pdf_text = '".$parts_list[$key]['pdf_text'.$i]."' and pdf_name = '".$parts_list[$key]['pdf_name'.$i]."' and pdf_size = '".$parts_list[$key]['pdf_size'.$i]."' and article_id = ".$count[$id_key]['id'];
									}
									$result = query_exec($cn,$sql);
									$pdf = result_assoc($result);
									if($pdf['count'] > 0){
										$error_flg = false;
									}else {
										$error_flg = true;
									}
								}
							}
							break;
					}
				}
			}
		}
		
		return($error_flg);
	}
?>