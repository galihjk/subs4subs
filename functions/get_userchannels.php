<?php
function get_userchannels($userid){
    $datalist = f("data_list")("channelposts");
    $userchannels = [];
    $useridcheck = $userid."-";
    foreach($userchannels as $item){
        if(f("str_is_diawali")($item,$useridcheck)){
            $userchannels[] = substr($item,strlen($useridcheck));
        }
    }
    return $userchannels;
}