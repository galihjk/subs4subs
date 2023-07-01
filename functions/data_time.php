<?php
function data_time($name, $empty = 0){
    $filename="data/$name.json";
	if(file_exists($filename)){
		$time = filemtime($filename);
		if($time === false){
			$return = $empty;
		}
		else{
			$return = $time;
		}
	}
	else{
		$return = $empty;
	}
	return $return;
}