<?php
function set_userchannel($userid, $channel, $postmsgid){
    return f("data_save")("channelposts/$userid-$channel",$postmsgid);
}