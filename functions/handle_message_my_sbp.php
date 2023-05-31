<?php function handle_message_my_sbp($botdata){
    $chatid = $botdata['chat']['id']; if(empty($chatid)) return false;
    $userid = $botdata['from']['id']; if(empty($userid)) return false;
    $text = $botdata["text"] ?? ""; if(empty($text)) return false;

    if($text == "/my_sbp"){
        $my_sbp = f("get_usersbp")($userid);
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$chatid,
            "text"=>"SBP Anda: $my_sbp\n/top_up",
        ]);
        return true;
    }

    if($text == "/top_up"){
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$chatid,
            "text"=>"Untuk TOP UP, silakan hubungi admin dan beritahu ID anda: $userid",
        ]);
        return true;
    }

    return false;
}