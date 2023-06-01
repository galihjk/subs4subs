<?php
function set_usersbp($userid, $sbp){
    $data = f("get_usersb")($userid);
    $data['SBP'] = $sbp;
    return f("set_usersb")($userid, $data);
}