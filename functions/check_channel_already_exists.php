<?php
function check_channel_already_exists($channel){
    $channel_check = substr($channel, 1); if (empty($channel_check)) return true;
    $datalist = f("data_list")("channelposts");
    foreach($datalist as $item){
        if(f("str_is_diakhiri")($item,"-$channel_check")){
            return $item;
        }
    }
    return false;
}