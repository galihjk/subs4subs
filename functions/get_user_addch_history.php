<?php
function get_user_addch_history($userid){
    $datalist = f("data_list")("user_addch_history");
    $user_addch_history = [];
    $useridcheck = $userid."-";
    foreach($datalist as $item){
        if(f("str_is_diawali")($item,$useridcheck)){
            $user_addch_history[] = substr($item,strlen($useridcheck));
        }
    }
    return $user_addch_history;
}