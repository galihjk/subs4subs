<?php
function gen_inline_keyboard($array, $max_col = 0, $jsonencode=false){
	if(empty($max_col)){
		$automaxcol = true;
	}
	else{
		$automaxcol = false;
	}
	if(!is_array($array)){
		$array = [$array];
	}
	$curcol = 0;
	$cols = [];
	$rows = [];
	foreach($array as $btnindex=>$button){
		if(!is_array($button)){
			$button = [$button];
		}
		if(count($button) == 1 and isset($button[0])){
			//default button for 1 non associative parameter
			$button = [
				'text'=>$button[0],
				'callback_data'=>$button[0]
			];
			unset($button[0]);
		}
		
		foreach($button as $key=>$val){
			if($key === 0 or $key === 1 or $key === 2){
				if(!isset($button['text'])){
					//non associative parameter: 1st = text
					$button['text'] = $button[$key];
				}
				elseif(!isset($button['url']) and !isset($button['callback_data'])){
					//non associative parameter: 2nd = callback_data or url
					if(strtolower(substr($button[$key],0,7)) == "http://" or strtolower(substr($button[$key],0,8)) == "https://"){
						$button['url'] = $button[$key];
					}
					else{
						$button['callback_data'] = $button[$key];
					}
				}
				elseif(!isset($button['width'])){
					//non associative parameter: 3rd = width
					$button['width'] = $button[$key];
				}
			}
		}
		if(empty($button['width'])){
			//default width
			$button['width'] = 1;
		}
		if($automaxcol and $button['width'] > $max_col){
			$max_col = $button['width'];
		}		
		if($button['width'] > $max_col){
			//max width
			$button['width'] = $max_col;
		}
		unset($button[0]);
		unset($button[1]);
		unset($button[2]);

		$nextbuttonwidth = 1;
		if(isset($array[$btnindex+1])){
			$nextbutton = $array[$btnindex+1];
			if(!is_array($nextbutton)){
				$nextbutton = [$nextbutton];
			}
			foreach($nextbutton as $key=>$val){
				if($key === 0 or $key === 1 or $key === 2){
					if(!isset($nextbutton['text'])){
						$nextbutton['text'] = "(next)";
					}
					elseif(!isset($nextbutton['url']) and !isset($nextbutton['callback_data'])){
						$nextbutton['url'] = "(next)";
					}
					elseif(!isset($nextbutton['width'])){
						$nextbutton['width'] = $nextbutton[$key];
					}
				}
			}
			if(empty($nextbutton['width'])){
				$nextbutton['width'] = 1;
			}
			$nextbuttonwidth = $nextbutton['width'];
		}

		$curcol += $button['width'];
		$cols[] = $button;
		if($curcol + $nextbuttonwidth > $max_col){
			//new line
			$rows[] = $cols;
			$curcol = 0;
			$cols = [];
		}
	}
	if($curcol < $max_col and !empty($cols)){
		$rows[] = $cols;
	}
	$keyboard = [
		'inline_keyboard' => $rows
	];
	// print_r($keyboard);
    if(!$jsonencode){
        return $keyboard;
    }
	$encodedKeyboard = json_encode($keyboard);
	return $encodedKeyboard;
}
