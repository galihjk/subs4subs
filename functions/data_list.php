<?php
function data_list($dir){
    $data_list = [];
    $scandir = scandir("data/$dir");
    foreach($scandir as $file){
        if(in_array($file, ['..', '.', 'info.txt'])) continue;
        $data_list[] = substr($file, 0, -4);
    }
    return $data_list;
}