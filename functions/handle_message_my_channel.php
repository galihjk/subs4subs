<?php function handle_message_my_channel($botdata){
    $chatid = $botdata['chat']['id']; if(empty($chatid)) return false;
    $userid = $botdata['from']['id']; if(empty($userid)) return false;
    $text = $botdata["text"] ?? ""; if(empty($text)) return false;

    if($text == "/my_channels"){
        $userchannels = f("get_userchannels")($userid);
        $output_text = "Channel Anda:\n\n";
        foreach($userchannels as $channel){
            $output_text .= "- $channel ";
            $msgid = f("data_load")("channelposts/$userid-$channel");
            $output_text .= "<a href='".f("s4slink")($msgid)."'>[↗️]</a>\n";
            $output_text .= "/hapus_channel_$channel\n\n";
        }
        $output_text .= "/add_channel - tambah channel";
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$chatid,
            "text"=>$output_text,
            "parse_mode"=>"HTML",
            "disable_web_page_preview"=>true,
        ]);
        return true;
    }
    return false;
}