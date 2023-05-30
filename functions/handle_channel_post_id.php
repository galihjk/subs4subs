<?php
function handle_channel_post_id($botdata){
    if(!empty($botdata['text']) 
    and $botdata['text'] == "/id"){
        $chat_id = $botdata["chat"]["id"];
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$chat_id,
            "text"=>$chat_id,
        ]);
        return true;
    }
    else{
        return false;
    }
}