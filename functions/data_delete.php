<?php
function data_delete($name){
    $filename="data/$name.json";
	return unlink($filename); 
}