<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	require_once("./tag_nl2br.php");
	require_once("./upload.php");
	require_once("./insert_check.php");
	
	function view_confirm($parts_list,$post,$select,$lang){
		$error_flg = true;
		$error_check = array();
		$img_cnt = 1;
		$pdf_cnt = 1;
		foreach($parts_list as $key => $val){
			switch($parts_list[$key]['type']){
				case "main":
					$main = nl2br($parts_list[$key]['main']);
					$main = tag_nl2br($main);
					$parts .= $main."\n";
					break;
				case "caption":
					$parts .= '<h3 class="headline1"><span class="text">'.$parts_list[$key]['caption']."</span></h3>\n";
					break;
				case "img":
					if($parts_list[$key]['img']['error'] == 0 && $parts_list[$key]['img'] != ""){
						$date = $post['year'].(str_pad($post['mon'],2,0,STR_PAD_LEFT)).(str_pad($post['day'],2,0,STR_PAD_LEFT));
						$check = img_upload($date,$parts_list[$key]['img'],$select,$lang,$img_cnt);
						if($check[1] == false){
							$error_flg = false;
							if(is_array($check[0])){
								foreach($check[0] as $check_key => $check_val){
									array_push($error_check,$check_val);
								}
							}
						}else {
							$align = img_align($parts_list[$key]['img_align']);
							switch($align){
								case "left" :
									$parts .= "<div class=\"image_left\"><img src=\"".$check[2]."\" ".$check[3]." alt=\"".$parts_list[$key]['img_alt']."\"></div>\n";
									break;
								case "center" :
									$parts .= "<div class=\"image\"><img src=\"".$check[2]."\" ".$check[3]." alt=\"".$parts_list[$key]['img_alt']."\"></div>\n";
									break;
								case "right" :
									$parts .= "<div class=\"image_right\"><img src=\"".$check[2]."\" ".$check[3]." alt=\"".$parts_list[$key]['img_alt']."\"></div>\n";
									break;
							}
							$hidden .= "<input type=\"hidden\" name=\"parts".$key."_path\" value=\"".$check[2]."\">\n";
							$hidden .= "<input type=\"hidden\" name=\"parts".$key."_img_filename\" value=\"".$parts_list[$key]['img']['name']."\">\n";
						}
						$img_cnt++;
					}else if($parts_list[$key]['img_name'] != ""){
						$align = img_align($parts_list[$key]['img_align']);
						switch($align){
							case "left" :
								$parts .= "<div class=\"imageArea left\">\n<div class=\"imageSet\" style=\"width:".$parts_list[$key]['img_width']."px\">\n<img src=\"".$parts_list[$key]['img_name']."\" width=\"".$parts_list[$key]['img_width']."\" height=\"".$parts_list[$key]['img_height']."\" alt=\"".$parts_list[$key]['img_alt']."\" class=\"noBorder\" />\n<!--/class=\"imageSet\"--></div>\n<!--/class=\"imageArea right\"--></div>\n";
								break;
							case "center" :
								$parts .= "<div class=\"imageArea center\">\n<div class=\"imageSet\" style=\"width:".$parts_list[$key]['img_width']."px\">\n<img src=\"".$parts_list[$key]['img_name']."\" width=\"".$parts_list[$key]['img_width']."\" height=\"".$parts_list[$key]['img_height']."\" alt=\"".$parts_list[$key]['img_alt']."\" class=\"noBorder\" />\n<!--/class=\"imageSet\"--></div>\n<!--/class=\"imageArea right\"--></div>\n";
								break;
							case "right" :
								$parts .= "<div class=\"imageArea right\">\n<div class=\"imageSet\" style=\"width:".$parts_list[$key]['img_width']."px\">\n<img src=\"".$parts_list[$key]['img_name']."\" width=\"".$parts_list[$key]['img_width']."\" height=\"".$parts_list[$key]['img_height']."\" alt=\"".$parts_list[$key]['img_alt']."\" class=\"noBorder\" />\n<!--/class=\"imageSet\"--></div>\n<!--/class=\"imageArea right\"--></div>\n";
								break;
						}
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
					if($parts_link != "<ul class=\"linkList\">\n</ul>\n"){
						$parts .= $parts_link;
					}
					break;
				case "pdf":
					$parts_pdf = "<div class=\"section\">\n";
					$parts_pdf .= "<ul>\n";
					for($i=1;$i<=PARTS_PDF_MAX;$i++){
						if($parts_list[$key]['pdf_file'.$i]['error'] == 0 && $parts_list[$key]['pdf_file'.$i] != ""){
							$date = $post['year'].(str_pad($post['mon'],2,0,STR_PAD_LEFT)).(str_pad($post['day'],2,0,STR_PAD_LEFT));
							$check = pdf_upload($date,$parts_list[$key]['pdf_file'.$i],$select,$lang,$pdf_cnt);
							if($check[1] == false){
								$error_flg = false;
								if(is_array($check[0])){
									foreach($check[0] as $check_key => $check_val){
										array_push($error_check,$check_val);
									}
								}
							}
						}
						if($parts_list[$key]['pdf_text'.$i] != "" && $check[2] != ""){
							$size = round($parts_list[$key]['pdf_file'.$i]['size'] / 1000);
							$parts_pdf .= "<li class=\"pdf\"><a href=\"".$check[2]."\" target=\"_blank\">".$parts_list[$key]['pdf_text'.$i]."(".$size."KB)</a></li>\n";
							$hidden .= "<input type=\"hidden\" name=\"parts".$key."_path".$i."\" value=\"".$check[2]."\">\n";
							$hidden .= "<input type=\"hidden\" name=\"parts".$key."_pdf_filename".$i."\" value=\"".$parts_list[$key]['pdf_file'.$i]['name']."\">\n";
							$hidden .= "<input type=\"hidden\" name=\"parts".$key."_pdf_size".$i."\" value=\"".$size."\">\n";
						}else if($parts_list[$key]['pdf_name'.$i] != "") {
							$parts_pdf .= "<li class=\"pdf\"><a href=\"".$parts_list[$key]['pdf_name'.$i]."\" target=\"_blank\">".$parts_list[$key]['pdf_text'.$i]."(".$parts_list[$key]['pdf_size'.$i]."KB)</a></li>\n";
						}
						$pdf_cnt++;
					}
					$parts_pdf .= "</ul>\n";
					$parts_pdf .= "</div><!-- class=\"section\" -->\n";
					if($parts_pdf != "<div class=\"section\">\n<ul>\n</ul>\n</div><!-- class=\"section\" -->\n"){
						$parts .= $parts_pdf;
					}
					break;
			}
		}
		return array($parts,$hidden,array($error_check,$error_flg));
	}

?>