<?php
function check_unsubscribe($botdata){
    $user = $botdata['from'];
    $userid = $user['id'];
    f("bot_kirim_perintah")("sendMessage",[
        "chat_id"=>$userid,
        "text"=>"Underconstruct",
    ]);
    return true;
}