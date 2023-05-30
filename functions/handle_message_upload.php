<?php function handle_message_upload($botdata){
    $text = $botdata["text"] ?? "";
    if(f("str_is_diawali")($text,"/upload_")){
        $explode = explode("_",$text);
        $upload_chatid = $explode[1];
        $upload_msgid = $explode[2];
        $delete_msgid = $explode[3];
        f("bot_kirim_perintah")("deleteMessage",[
            'chat_id'=>$upload_chatid,
            'message_id'=>$delete_msgid,
        ]);
        f("bot_kirim_perintah")("deleteMessage",[
            'chat_id'=>$botdata["chat"]["id"],
            'message_id'=>$botdata["message_id"],
        ]);
        $storage = f("get_config")("storage");
        $result = f("bot_kirim_perintah")("forwardMessage",[
            "chat_id"=>$storage,
            "from_chat_id"=>$upload_chatid,
            "message_id"=>$upload_msgid,
        ]);
        $link = "t.me/".f("get_config")("botuname")."?start=".$result["result"]["message_id"];
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$storage,
            "text"=>"<b>LINK:</b> ".$link,
            "parse_mode"=>"HTML",
            "disable_web_page_preview"=>true,
            "reply_to_message_id"=>$result["result"]["message_id"],
        ]);
        $is_admin = in_array($botdata['from']['id'],f("get_config")("bot_admins",[]));
        $textresult = "<b>LINK:</b> ".$link;
        if($is_admin){
            $textresult .= "\n";
            $textresult .= "/set_custom_link_".$result["result"]["message_id"];
        }
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$upload_chatid,
            "text"=>$textresult,
            "parse_mode"=>"HTML",
            "reply_to_message_id"=>$upload_msgid,
            "disable_web_page_preview"=>true,
        ]);
        return true;
    }
    return false;
}