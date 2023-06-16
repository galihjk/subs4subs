<?php
function get_usersb($userid){
    $user = f("data_load")("usersubs/$userid","new");
    if(!is_array($user) and $user === "new"){
        $user = [
            'SBP' => 0,
            'subs' => [],
            'chll' => [],
        ];
        f("data_save")("usersubs/$userid",$user);
    }
    return $user;
}