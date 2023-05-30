<?php
function handle_message_getfile($botdata){
    if(!empty($botdata['text']) and f("str_is_diawali")($botdata['text'],"/start ")){
        $get_msg_id = str_replace("/start ","",$botdata['text']);
        $chat_id = $botdata["chat"]["id"];

        if(preg_match('/[a-z]/i', substr($get_msg_id,0,1))){
            $get_msg_id = f("data_load")($get_msg_id,false);
            if(empty($get_msg_id)){
                f("bot_kirim_perintah")("sendMessage",[
                    "chat_id"=>$chat_id,
                    "text"=>"Tidak ditemukan",
                ]);
                return true;
            }
        }

        $result = f("bot_kirim_perintah")("copyMessage",[
            "chat_id"=>$chat_id,
            "from_chat_id"=>f("get_config")("storage"),
            "message_id"=>$get_msg_id,
        ]);
        if(empty($result['ok'])){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chat_id,
                "text"=>"Tidak ditemukan..",
            ]);
            return true;
        }

        $result = f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>f("get_config")("storage"),
            "text"=>"Didownload oleh $chat_id",
            "reply_to_message_id"=>$get_msg_id,
        ]);

        return true;
    }
    else{
        return false;
    }
}