<?php function handle_message_get_sbp($botdata){
    $chatid = $botdata['chat']['id']; if(empty($chatid)) return false;
    $userid = $botdata['from']['id']; if(empty($userid)) return false;
    $text = $botdata["text"] ?? ""; if(empty($text)) return false;

    if(f("str_is_diawali")($text,"/start ")){
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$chatid,
            "text"=>"underconst: get_sbp $text",
            "parse_mode"=>"HTML",
            "disable_web_page_preview"=>true,
        ]);
        return true;
    }
    return false;
}