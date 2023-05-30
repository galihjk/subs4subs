<?php
function data_save($name, $data){
    $filename="data/$name.json";
	return file_put_contents($filename, json_encode($data)); 
}