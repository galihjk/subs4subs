<?php function handle_message_get_sbp($botdata){
    $chatid = $botdata['chat']['id']; if(empty($chatid)) return false;
    $userid = $botdata['from']['id']; if(empty($userid)) return false;
    $text = $botdata["text"] ?? ""; if(empty($text)) return false;

    if(f("str_is_diawali")($text,"/start ")){
        $startdata = substr($text,strlen("/start "));
        if(empty($startdata)) return false;
        $explode = explode("-",$startdata);
        if(empty($explode[1])) return false;
        $ch_owner = $explode[0];
        $channel = $explode[1];
        $ch_msgid = f("get_userchannelmsgid")($ch_owner,$channel);
        if(!$ch_msgid) {
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"Gagal: Kadaluarsa. Silakan cek lagi channel ".f("get_config")("s4s_channel"),
                "parse_mode"=>"HTML",
                "disable_web_page_preview"=>true,
            ]);
            return true;
        }
        if($userid == $ch_owner){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"@$channel adalah channel Anda sendiri. Jika orang lain menggunakan link / tombol ini, dan ia sudah subscribe channel Anda ini, maka ia bisa dapat 1 SBP, dan SBP Anda akan berkurang 1.",
                "parse_mode"=>"HTML",
                "disable_web_page_preview"=>true,
            ]);
            return true;
        }
        $owner_sbp = f("get_usersbp")($ch_owner);
        if($owner_sbp < 1){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"Sudah tidak bisa, silakan cek lagi channel ".f("get_config")("s4s_channel"),
                "parse_mode"=>"HTML",
                "disable_web_page_preview"=>true,
            ]);
            return true;
        }
        $getChatMember = f("bot_kirim_perintah")("getChatMember",[
            'chat_id'=>"@$channel",
            'user_id'=>$userid,
        ]);
        if(empty($getChatMember["result"]["status"])){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"Gagal: Bot sudah bukan admin di channel @$channel",
                "parse_mode"=>"HTML",
                "disable_web_page_preview"=>true,
                'reply_markup' => [
                    'force_reply'=>false,
                ],
            ]);
            return true;
        }
        if(in_array($getChatMember["result"]["status"],["creator","owner","administrator"])){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"Gagal: Anda tidak bisa mendapatkan SBP dari channel yang anda kelola sendiri (administrator / owner).",
                "parse_mode"=>"HTML",
                "disable_web_page_preview"=>true,
                'reply_markup' => [
                    'force_reply'=>false,
                ],
            ]);
            return true;
        }
        if(in_array($getChatMember["result"]["status"],["restricted","left","kicked"])){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"Gagal: Anda harus join dulu @$channel. "
                    ."Status Anda saat ini adalah ".$getChatMember["result"]["status"]
                    .". Silakan subscribe dahulu, setelah itu "
                    ."<a href='t.me/".f("get_config")("botuname")."?start=$ch_owner-$channel'>coba lagi</a>.",
                "parse_mode"=>"HTML",
                "disable_web_page_preview"=>true,
                'reply_markup' => [
                    'force_reply'=>false,
                ],
            ]);
            return true;
        }
        $subscibed_channel = f("get_usersubs")($userid);
        if(in_array($channel,$subscibed_channel)){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"Anda sudah pernah mendapatkan SBP dari channel @$channel",
                "parse_mode"=>"HTML",
                "disable_web_page_preview"=>true,
            ]);
            return true;
        }

        $owner_sbp--;
        f("set_usersbp")($ch_owner,$owner_sbp);
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$ch_owner,
            "text"=>"Channel anda [@$channel] telah disubscribe (SBP-1).\nSisa SBP: $owner_sbp",
            "parse_mode"=>"HTML",
            "disable_web_page_preview"=>true,
        ]);

        $user_sbp = f("get_usersbp")($userid);
        $user_sbp++;
        $subscibed_channel[] = $channel;
        f("set_usersb")($userid,[
            'SBP' => $user_sbp,
            'subs' => $subscibed_channel,
        ]);
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$userid,
            "text"=>"Anda mendapatkan 1 SBP dari @$channel.\n"
                ."SBP Anda: $user_sbp",
            "parse_mode"=>"HTML",
            "disable_web_page_preview"=>true,
        ]);
        
        $inviter = f("data_load")("inviteref/$userid",false);
        if($inviter){
            $invitersbp = f("get_usersbp")($inviter);
            if($invitersbp > 0){
                f("bot_kirim_perintah")("sendMessage",[
                    "chat_id"=>$inviter,
                    "text"=>"Anda berhasil mendapatkan 1 SBP karena telah mengundang pengguna baru $userid",
                ]);
                $invitersbp++;
                f("set_usersbp")($inviter,$invitersbp);
            }
            else{
                f("bot_kirim_perintah")("sendMessage",[
                    "chat_id"=>$inviter,
                    "text"=>"Gagal mendapatkan SBP dari pengguna baru $userid karena SBP Anda $invitersbp (minimal 1)",
                ]);
            }
            f("data_delete")("inviteref/$userid",false);
        }

        f("check_sbp_habis")($ch_owner);

        return true;
    }
    return false;
}