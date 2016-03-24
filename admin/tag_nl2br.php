<?php 
	require_once($_SERVER['DOCUMENT_ROOT']."/phplib/lib.php");
	
	
	//ブロック要素のタグの後から<br>を削除
	function tag_nl2br($main) {
		$main = preg_replace_callback("/<\/*([\w]+)[^>]*><br \/>\r\n/","br_replace",$main);
		return $main;
	}

	//ブロック要素のタグの後から<br>を削除
	function br_replace($matchs){
		$flg = false;
		$tag = array();
		array_push($tag,"blockquote","form","noframes","noscript","div","center","fieldset","address");
		array_push($tag,"h1","h2","h3","h4","h5","h6","p","pre","dir","dl","menu","ol","table","ul");
		array_push($tag,"li","dt","dd","caption","col","colgroup","thead","tr","th","td","tfoot","tbody");
		array_push($tag,"hr","isindex","applet","button","iframe","ins","map","object","script");
		
		if(is_array($tag)){
			foreach($tag as $key => $val){
				if($val == $matchs[1]){
					$flg = true;
					break;
				}
			}
			if($flg == true){
				$matchs[0] = str_replace("><br />\r\n",">\r\n",$matchs[0]);
			}
		}
		return($matchs[0]);
	}

?>