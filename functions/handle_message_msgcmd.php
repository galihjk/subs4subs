<?php
function handle_message_msgcmd($botdata){
    $text = $botdata["text"] ?? "";
    if(!empty($text)){
        foreach(f("get_config")("msgcmd",[]) as $msgcmd=>$cmd){
            if($text == $msgcmd){
                $chat_id = $botdata["chat"]["id"];
                $method = $cmd[0];
                $params = $cmd[1];
                $params['chat_id'] = $chat_id;
                f("bot_kirim_perintah")($method,$params);
                return true;
            }
        }
    }
    return false;
}