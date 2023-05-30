<?php
function str_is_diakhiri($string, $diakhiri, $caseSensitive = true){
	if(!$caseSensitive){
		$string = strtolower($string);
		$diakhiri = strtolower($diakhiri);
	}
	if(substr($string,-strlen($diakhiri)) === $diakhiri){
		return true;
	}
	else{
		return false;
	}
}