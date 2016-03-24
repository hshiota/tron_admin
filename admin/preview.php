<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	require_once("./auth.php");
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lang.php");
	require_once("./tag_nl2br.php");
	require_once("./upload.php");
	require_once("./sort.php");
	require_once("./insert_check.php");
	require_once("./include.php");

	session_cache_limiter('private, must-revalidate');
	session_name("tel_admin");
	session_start();

	$cn = db_connect();

	if($_SESSION['code'] != ""){
		$lang = $_SESSION['code'];
	}else {
		header("Location:./top.php");
	}
	//文字コード
	$Tpl->assign("charset",CHARSET_SELECT);
	//言語
	$Tpl->assign("lang",$lang);

	//ニュースカテゴリ読み込み
	$sql = "select * from news_category order by id";
	$result = query_exec($cn,$sql);
	$news_category = result_all($result);
	$Tpl->assign("ary_news_category",$news_category);

	$Tpl->assign("select",$_POST['select']);
	/*------------------------------------------------------*/
	//プレビュー画面
	/*------------------------------------------------------*/
//	if($_SESSION['post'] != ""){
//		$post = $_SESSION['post'];
//	}
	$post = $_POST;
	if($post != ""){
		$value_post = exchange_view($post);
		$value_m_post = exchange_m_hidden($post);
	}
	//パーツクエリチェック
	if(is_array($post)){
		$parts_list = array();
		foreach($value_post as $key => $val){
			if(preg_match("/^parts([0-9]+)_([a-z|_|0-9]+)$/",$key,$match)){
				if($match[2] == "main" || $match[2] == "img_size"){
					$parts_list[$match[1]][$match[2]] = $value_m_post[$key];
				}else {
					$parts_list[$match[1]][$match[2]] = $value_post[$key];
				}
			}
		}
	}

	if(is_array($parts_list)){
		$parts = "";
		uasort($parts_list,"sort_new");
		foreach($parts_list as $key => $val){
			switch($parts_list[$key]['type']){
				case "main":
					$main = nl2br($parts_list[$key]['main']);
					$main = tag_nl2br($main);
					$parts .= "".$main."\n";
					break;
				case "caption":
					$parts .= '<h3 class="headline1"><span class="text">'.$parts_list[$key]['caption']."</span></h3>\n";
					break;
				case "img":
					$align = img_align($parts_list[$key]['img_align']);
					switch($align){
						case "left" :
							if($parts_list[$key]['path'] != ""){
								$parts .= "<div class=\"image_left\"><img src=\"".$parts_list[$key]['path']."\" ".$parts_list[$key]['img_size']." alt=\"".$parts_list[$key]['img_alt']."\" /></div>\n";
							}else if($parts_list[$key]['img_name'] != ""){
								$parts .= "<div class=\"image_left\"><img src=\"".$parts_list[$key]['img_name']."\" width=\"".$parts_list[$key]['img_width']."\" height=\"".$parts_list[$key]['img_height']."\" alt=\"".$parts_list[$key]['img_alt']."\" /></div>\n";
							}
							break;
						case "center" :
							if($parts_list[$key]['path'] != ""){
								$parts .= "<div class=\"image\"><img src=\"".$parts_list[$key]['path']."\" ".$parts_list[$key]['img_size']." alt=\"".$parts_list[$key]['img_alt']."\"></div>\n";
							}else if($parts_list[$key]['img_name'] != ""){
								$parts .= "<div class=\"image\"><img src=\"".$parts_list[$key]['img_name']."\" width=\"".$parts_list[$key]['img_width']."\" height=\"".$parts_list[$key]['img_height']."\" alt=\"".$parts_list[$key]['img_alt']."\" /></div>\n";
							}
							break;
						case "right" :
							if($parts_list[$key]['path'] != ""){
								$parts .= "<div class=\"image_right\"><img src=\"".$parts_list[$key]['path']."\" ".$parts_list[$key]['img_size']." alt=\"".$parts_list[$key]['img_alt']."\"></div>\n";
							}else if($parts_list[$key]['img_name'] != ""){
								$parts .= "<div class=\"image_right\"><img src=\"".$parts_list[$key]['img_name']."\" width=\"".$parts_list[$key]['img_width']."\" height=\"".$parts_list[$key]['img_height']."\" alt=\"".$parts_list[$key]['img_alt']."\" /></div>\n";
							}
							break;
					}
					break;
				case "link":
					$parts_link = "<ul class=\"linkList\">\n";
					for($i=1;$i<=PARTS_LINK_MAX;$i++){
						if($parts_list[$key]['link_text'.$i] != "" && $parts_list[$key]['link_url'.$i] != ""){
							$parts_link .= "<li><a href=\"".$parts_list[$key]['link_url'.$i]."\"";
							if($parts_list[$key]['link_target'.$i] == 1){
								$parts_link .= " target=\"_blank\" class=\"link\"";
							}
							$parts_link .= ">".$parts_list[$key]['link_text'.$i]."</a></li>\n";
						}
					}
					$parts_link .= "</ul>\n";
					if($parts_link != "<ul>\n</ul>\n"){
						$parts .= $parts_link;
					}
					break;
				case "pdf":
					$parts_pdf = "<ul class=\"linkList\">\n";
					for($i=1;$i<=PARTS_PDF_MAX;$i++){
						if($parts_list[$key]['pdf_text'.$i] != "" && $parts_list[$key]['path'.$i] != ""){
							$parts_pdf .= "<li><a href=\"".$parts_list[$key]['path'.$i]."\" target=\"_blank\" class=\"pdfLink\">".$parts_list[$key]['pdf_text'.$i]."(".$parts_list[$key]['pdf_size'.$i]."KB)</a></li>\n";
						}else if($parts_list[$key]['pdf_name'.$i] != "") {
							$parts_pdf .= "<li><a href=\"".$parts_list[$key]['pdf_name'.$i]."\" target=\"_blank\" class=\"pdfLink\">".$parts_list[$key]['pdf_text'.$i]."(".$parts_list[$key]['pdf_size'.$i]."KB)</a></li>\n";
						}
					}
					$parts_pdf .= "</ul>\n";
					if($parts_pdf != "<ul class=\"linkList\">\n</ul>\n"){
						$parts .= $parts_pdf;
					}
					break;
			}
		}
		$Tpl->assign("parts",$parts);
	}

	if($post != ""){
		foreach($value_post as $key => $val){
			if($key == "mon" || $key == "day"){
				$Tpl->assign($key,str_pad($val,2,0,STR_PAD_LEFT));
			}else {
				$Tpl->assign($key,$val);
			}
		}
		$Tpl->assign("date",$value_post['year']."/".str_pad($value_post['mon'],2,0,STR_PAD_LEFT)."/".str_pad($value_post['day'],2,0,STR_PAD_LEFT));
	}
	
	$Tpl->register_outputfilter("mb_convert_out");
	$Tpl->display('admin/news_preview.tpl.html');

	db_close ($cn);
	
	
?>
