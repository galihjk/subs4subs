<?php
function get_usersubs($userid){
    $get_usersb = f("get_usersb")($userid);
    return $get_usersb['subs'];
}