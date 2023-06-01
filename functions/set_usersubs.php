<?php
function set_usersubs($userid, $subs){
    $data = f("get_usersb")($userid);
    $data['subs'] = $subs;
    return f("set_usersb")($userid, $data);
}