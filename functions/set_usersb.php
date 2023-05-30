<?php
function set_usersb($userid, $data){
    return f("data_save")("usersubs/$userid",$data);
}