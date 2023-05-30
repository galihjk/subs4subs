<?php
function data_load($name, $empty = []){
    $filename="data/$name.json";
	if(file_exists($filename)){
		$filedata = file_get_contents($filename);
		$data = json_decode($filedata,true);
		if($data === false){
			$data = $empty;
		}
	}
	else{
		$data = $empty;
	}
	return $data;
}