<?php
function check_unsubscribe($botdata){
    $user = $botdata['from'];
    $userid = $user['id'];
    f("check_unsubscribe_user")($userid);
    return false;
}