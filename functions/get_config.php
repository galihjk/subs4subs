<?php
$GLOBALS['global_config'] = include('config.php');

function get_config($key, $default = null){
    global $global_config;
    if(!isset($global_config[$key])) return $default;
    return $global_config[$key];
}