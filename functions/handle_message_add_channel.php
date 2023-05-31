<?php function handle_message_add_channel($botdata){
        $chatid = $botdata['chat']['id']; if(empty($chatid)) return false;
        $userid = $botdata['from']['id']; if(empty($userid)) return false;
        $text = $botdata["text"] ?? ""; if(empty($text)) return false;
    
        if($text =="/add_channel"){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"underconst: add_channel",
                "parse_mode"=>"HTML",
                "disable_web_page_preview"=>true,
            ]);
            return true;
        }
        return false;
}