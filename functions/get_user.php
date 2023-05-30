<?php
function get_user($userid){
    $user = data_load("users/$userid","new");
    if(!is_array($user) and $user === "new"){
        $user = [
            'SBP' => 0,
            'subs' => [],
        ];
        data_save("users/$userid",$user);
    }
    return $user;
}