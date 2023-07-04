<?php
function check_banned_user($botdata){
    $chatid = $botdata['chat']['id']; if(empty($chatid)) return false;
    $userid = $botdata['from']['id']; if(empty($userid)) return false;
    if ($chatid == f("get_config")("admin_chat_id")) return false;
    if(file_exists("data/banned_users/$userid")){
        f("bot_kirim_perintah")("sendMessage",[
            'chat_id'=>$chatid,
            'text'=>"Your account ($chatid) is banned",
            "reply_to_message_id"=>$botdata['message_id'],
        ]);
        return true;
    }
    return false;
}