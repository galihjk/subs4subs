<?php function handle_message_hapus_channel($botdata){
    $chatid = $botdata['chat']['id']; if(empty($chatid)) return false;
    $userid = $botdata['from']['id']; if(empty($userid)) return false;
    $text = $botdata["text"] ?? ""; if(empty($text)) return false;

    if(f("str_is_diawali")($text,"/hapus_channel_")){
        $channel = substr($text,strlen("/hapus_channel_"));
        $userchannel = "";
        $datalist = f("data_list")("channelposts");
        $channelcheck = "-".$channel;
        foreach($datalist as $item){
            if(f("str_is_diakhiri")($item,$channelcheck)){
                $userchannel = $item;
                break;
            }
        }
        if(empty($userchannel)){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"Command ini sudah tidak dapat digunakan.\n/my_channels - lihat daftar channel",
                "parse_mode"=>"HTML",
                "reply_to_message_id"=>$botdata['message_id'],
            ]);
            return true;
        }
        $userchannel = str_replace("-","_",$userchannel);
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$chatid,
            "text"=>"Anda yakin? Jika iya, gunakan command berikut untuk menghapusnya: /hapus_yakin_$userchannel",
            "parse_mode"=>"HTML",
            "disable_web_page_preview"=>true,
        ]);
        return true;
    }
    elseif(f("str_is_diawali")($text,"/hapus_yakin_")){
        $userchannel = substr($text,strlen("/hapus_yakin_"));
        $userchannel = str_replace("_","-",$userchannel);
        $userchannelpost = f("data_load")("channelposts/$userchannel");
        if(empty($userchannelpost)){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"Command ini sudah tidak dapat digunakan.\n/my_channels - lihat daftar channel",
                "parse_mode"=>"HTML",
                "reply_to_message_id"=>$botdata['message_id'],
            ]);
            return true;
        }
        $deleteMsg = f("bot_kirim_perintah")('deleteMessage',[
            'chat_id' => f("get_config")("s4s_channel"),
            'message_id' => $userchannelpost,
        ]);
        if(empty($deleteMsg['ok'])){
            f("bot_kirim_perintah")('editMessageText',[
                'chat_id' => f("get_config")("s4s_channel"),
                'text'=> "This message has been #deleted",
                'parse_mode'=>'HTML',
                'message_id' => $userchannelpost,
            ]);
        }
        f("data_delete")("channelposts/$userchannel");
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$chatid,
            "text"=>"Channel berhasil dihapus",
            "parse_mode"=>"HTML",
            "reply_to_message_id"=>$botdata['message_id'],
        ]);
        return true;
    }
    return false;
}