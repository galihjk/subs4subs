<?php
function set_userchannel($userid, $channel, $postmsgid){
    return f("data_save")("$userid-$channel",$postmsgid);
}