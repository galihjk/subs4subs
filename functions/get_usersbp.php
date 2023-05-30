<?php
function get_usersbp($userid){
    $get_usersb = f("get_usersb")($userid);
    return $get_usersb['SBP'];
}