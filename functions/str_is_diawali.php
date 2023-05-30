<?php
function str_is_diawali($string, $diawali, $caseSensitive = true){
	if(!$caseSensitive){
		$string = strtolower($string);
		$diawali = strtolower($diawali);
	}
	if(substr($string,0,strlen($diawali)) === $diawali){
		return true;
	}
	else{
		return false;
	}
}