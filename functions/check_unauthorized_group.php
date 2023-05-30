<?php 
function check_unauthorized_group($botdata){
    $chat_id = $botdata["chat"]["id"];
    if(!empty($chat_id)){
        if(f("str_is_diawali")($chat_id,"-")
        and f("get_config")("admin_chat_id","") != $chat_id
        ){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chat_id,
                "text"=>"yuk, ke sini ==> ".f("get_config")("s4s_channel"),
            ]);
            f("bot_kirim_perintah")("leaveChat",[
                "chat_id"=>$chat_id,
            ]);
            return true;
        }
    }
    return false;
}