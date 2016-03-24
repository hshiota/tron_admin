<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	
	// 画像アップ
	function img_upload($date,$files,$select,$lang,$img_cnt) {
		$error_msg = array();
		$error_flg = true;
		$lang_path = ($lang == 'jpn' || $lang == 'eng') ? '' : '/'.$lang;
		// make dir
		if($select == "news"){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image",0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date,0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date,0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date."/temp")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date."/temp",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date."/temp",0777);
			}
		}else if ($select == "ir"){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image",0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date,0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date,0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date."/temp")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date."/temp",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date."/temp",0777);
			}
		}

		if($files){
			$fname = $files['tmp_name'];
			if($fname != ""){
				if($select == "news"){
					$fullpath = $_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date."/temp/temp_".$img_cnt."_".$files['name'];
					$path = $lang_path."/".$select."/image/".$date."/temp/temp_".$img_cnt."_".$files['name'];
				}else if ($select == "ir"){
					$fullpath = $_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date."/temp/temp_".$img_cnt."_".$files['name'];
					$path = $lang_path."/".$select."/news/image/".$date."/temp/temp_".$img_cnt."_".$files['name'];
				}

				// もしファイルがあったら消す
				if(file_exists($fullpath)) {
					unlink($fullpath);
				}
				@rename($fname, $fullpath);
				@chmod($fullpath,0644);

				// ファイルサイズを取得する
				if(file_exists($fullpath)) {
					$img_size = getimagesize($fullpath);
					//画像チェック
					if($img_size[0] > IMG_MAX){
						unlink($fullpath);
						array_push($error_msg,"Please adjust the width of the image with ".IMG_MAX."px or less.");
						$error_flg = false;
					}
					// (1:gif, 2:jpg, 3:png)
					switch($img_size[2]) {
						case 1 :
							break;
						case 2 :
							break;
						case 3 :
							break;
						default:
							unlink($fullpath);
							array_push($error_msg,"The file type of this image file is not supported. (Only jpg, gif, png)");
							$error_flg = false;
							break;
					}
					//サイズ
					$size = $img_size[3];
				}else {
					$error_flg = false;
				}

			}
		}
		return(array($error_msg,$error_flg,$path,$size));
	}
	
	//tempの削除
	function temp_delete($path){
		if(file_exists($_SERVER['DOCUMENT_ROOT'].$path)){
			unlink($_SERVER['DOCUMENT_ROOT'].$path);
		}
		$dirpath = dirname($path);
		@rmdir($_SERVER['DOCUMENT_ROOT'].$dirpath);
		//$dirpath_u = dirname($dirpath);
		//@rmdir($_SERVER['DOCUMENT_ROOT'].$dirpath_u);
	}

	//画像の移動と一時アップ画像の削除
	function img_move($date,$path,$name,$select,$id,$lang,$img_cnt){
		$lang_path = ($lang == 'jpn' || $lang == 'eng') ? '' : '/'.$lang;
		if($select == "news"){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image",0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date,0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date,0777);
			}
		}else if ($select == "ir"){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image",0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date,0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date,0777);
			}
		}
		$filename = $_SERVER['DOCUMENT_ROOT'].$path;
		if($select == "news"){
			$new_filename = $_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date."/".$id."_".$img_cnt."_".$name;
			$new_filepath = $lang_path."/".$select."/image/".$date."/".$id."_".$img_cnt."_".$name;
		}else if ($select == "ir"){
			$new_filename = $_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date."/".$id."_".$img_cnt."_".$name;
			$new_filepath = $lang_path."/".$select."/news/image/".$date."/".$id."_".$img_cnt."_".$name;
		}
		//tempファイルを本番用にリネームする
		if($filename != $new_filename){
			if(file_exists($filename)){
				if(file_exists($new_filename)){
					unlink($new_filename);
				}
				rename($filename,$new_filename);
				chmod($new_filename,0644);
			}
		}
		$dirpath = dirname($filename);
		@rmdir($dirpath);
		return($new_filepath);
	}
	
	//コピー
	function img_copy($date,$path,$name,$select,$id,$lang,$img_cnt){
		$lang_path = ($lang == 'jpn' || $lang == 'eng') ? '' : '/'.$lang;
		if($select == "news"){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image",0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date,0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date,0777);
			}
		}else if ($select == "ir"){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image",0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date,0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date,0777);
			}
		}
		$filename = $_SERVER['DOCUMENT_ROOT'].$path;
		if($select == "news"){
			$new_filename = $_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/image/".$date."/".$id."_".$img_cnt."_".$name;
			$new_filepath = $lang_path."/".$select."/image/".$date."/".$id."_".$img_cnt."_".$name;
		}else if ($select == "ir"){
			$new_filename = $_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/image/".$date."/".$id."_".$img_cnt."_".$name;
			$new_filepath = $lang_path."/".$select."/news/image/".$date."/".$id."_".$img_cnt."_".$name;
		}
		if(file_exists($filename)){
			copy($filename,$new_filename);
		}
		return($new_filepath);
	}

	
	// PDFアップ
	function pdf_upload($date,$files,$select,$lang,$pdf_cnt) {
		$error_msg = array();
		$error_flg = true;
		$lang_path = ($lang == 'jpn' || $lang == 'eng') ? '' : '/'.$lang;
		// make dir
		if($select == "news"){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf",0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date,0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date,0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date."/temp")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date."/temp",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date."/temp",0777);
			}
		}else if ($select == "ir"){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf",0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date,0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date,0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date."/temp")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date."/temp",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date."/temp",0777);
			}
		}
		
		if($files){
			$fname = $files['tmp_name'];
			if($fname != ""){
				if($select == "news"){
					$fullpath = $_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date."/temp/temp_".$pdf_cnt."_".$files['name'];
					$path = $lang_path."/".$select."/pdf/".$date."/temp/temp_".$pdf_cnt."_".$files['name'];
				}else if ($select == "ir"){
					$fullpath = $_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date."/temp/temp_".$pdf_cnt."_".$files['name'];
					$path = $lang_path."/".$select."/news/pdf/".$date."/temp/temp_".$pdf_cnt."_".$files['name'];
				}

				// もしファイルがあったら消す
				if(file_exists($fullpath)) {
					unlink($fullpath);
				}
				@rename($fname, $fullpath);
				@chmod($fullpath,0644);

			}
		}
		return(array($error_msg,$error_flg,$path));
	}

	//PDFの移動と一時アップPDFの削除
	function pdf_move($date,$path,$name,$select,$id,$lang,$pdf_cnt){
		$lang_path = ($lang == 'jpn' || $lang == 'eng') ? '' : '/'.$lang;
		if($select == "news"){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf",0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date,0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date,0777);
			}
		}else if ($select == "ir"){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf",0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date,0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date,0777);
			}
		}
		$filename = $_SERVER['DOCUMENT_ROOT'].$path;
		if($select == "news"){
			$new_filename = $_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date."/".$id."_".$pdf_cnt."_".$name;
			$new_filepath = $lang_path."/".$select."/pdf/".$date."/".$id."_".$pdf_cnt."_".$name;
		}else if ($select == "ir"){
			$new_filename = $_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date."/".$id."_".$pdf_cnt."_".$name;
			$new_filepath = $lang_path."/".$select."/news/pdf/".$date."/".$id."_".$pdf_cnt."_".$name;
		}
		//tempファイルを本番用にリネームする
		if($filename != $new_filename){
			if(file_exists($filename)){
				if(file_exists($new_filename)){
					unlink($new_filename);
				}
				rename($filename,$new_filename);
				chmod($new_filename,0644);
			}
		}
		$dirpath = dirname($filename);
		@rmdir($dirpath);
		return($new_filepath);
	}
	
	//コピー
	function pdf_copy($date,$path,$name,$select,$id,$lang,$pdf_cnt){
		$lang_path = ($lang == 'jpn' || $lang == 'eng') ? '' : '/'.$lang;
		if($select == "news"){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf",0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date,0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date,0777);
			}
		}else if ($select == "ir"){
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf")){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf",0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf",0777);
			}
			if(!file_exists($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date)){
				mkdir($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date,0777);
				chmod($_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date,0777);
			}
		}
		$filename = $_SERVER['DOCUMENT_ROOT'].$path;
		if($select == "news"){
			$new_filename = $_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/pdf/".$date."/".$id."_".$pdf_cnt."_".$name;
			$new_filepath = $lang_path."/".$select."/pdf/".$date."/".$id."_".$pdf_cnt."_".$name;
		}else if ($select == "ir"){
			$new_filename = $_SERVER['DOCUMENT_ROOT'].$lang_path."/".$select."/news/pdf/".$date."/".$id."_".$pdf_cnt."_".$name;
			$new_filepath = $lang_path."/".$select."/news/pdf/".$date."/".$id."_".$pdf_cnt."_".$name;
		}
		if(file_exists($filename)){
			copy($filename,$new_filename);
		}
		return($new_filepath);
	}

	/*------------------------------------------------------*/
	//プレビュー処理
	/*------------------------------------------------------*/
	function preview_post($post,$parts_list,$select,$lang){
		if(is_array($parts_list)){
			$img_cnt = 1;
			$pdf_cnt = 1;
			foreach($parts_list as $key => $val){
				switch($parts_list[$key]['type']){
					case "img":
						if($parts_list[$key]['path'] != ""){
							$size = getimagesize($_SERVER['DOCUMENT_ROOT'].$parts_list[$key]['path']);
							$post['parts'.$key.'_path'] = $parts_list[$key]['path'];
							$post['parts'.$key.'_img_size'] = $size;
							break;
						}
				case "pdf":
					for($i=1;$i<=PARTS_PDF_MAX;$i++){
						if($parts_list[$key]['path'.$i] != ""){
							$size = round(filesize($_SERVER['DOCUMENT_ROOT'].$parts_list[$key]['path'.$i]) / 1000);
							$post['parts'.$key.'_path'.$i] = $parts_list[$key]['path'.$i];
							$post['parts'.$key.'_pdf_size'.$i] = $size;
							$pdf_cnt++;
						}
					}
					break;
				}
			}
		}
		return $post;
	}
?>