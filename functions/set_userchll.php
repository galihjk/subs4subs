<?php
function set_userchll($userid, $chll){
    $data = f("get_usersb")($userid);
    if(empty($data['chll'])) $data['chll'] = [];
    $data['chll'][$chll]=1;
    return f("set_usersb")($userid, $data);
}