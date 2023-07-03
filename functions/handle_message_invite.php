<?php function handle_message_invite($botdata){
    $chatid = $botdata['chat']['id']; if(empty($chatid)) return false;
    $userid = $botdata['from']['id']; if(empty($userid)) return false;
    $text = $botdata["text"] ?? ""; if(empty($text)) return false;

    if($text == "/invite"){
        $outputtext = "Anda bisa mendapatkan 1 SBP setiap mengajak pengguna baru.\nSyarat:\n";
        $outputtext .= "• Pengguna baru adalah pengguna yang belum pernah melakukan start pada bot @".f("get_config")("botuname")."\n";
        $outputtext .= "• Pengguna baru melakukan start bot pertama kali dari link anda\n";
        $outputtext .= "• Saat pengguna baru mendapatkan SBP dari hasi join channel untuk pertama kali, Anda harus memiliki minimal 1 SBP\n";
        $outputtext .= "Berikut ini link Anda untuk mengundang pengguna baru:\n";
        $outputtext .= "https://t.me/".f("get_config")("botuname")."/?start=ref_$userid";
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$chatid,
            "text"=>"",
            "disable_web_page_preview"=>true,
        ]);
        return true;
    }
    elseif(f("str_is_diawali")($text,"/start ref_")){
        $inviter = str_replace("/start ref_", "",$text);
        if($inviter == $userid){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"Anda melakukan start bot dari link Anda sendiri. Jika pengguna baru melakukan start dengan link ini, Anda bisa mendapatkan 1 SBP. Pastikan Anda memiliki minimal 1 SBP saat pengguna baru tersebut mendapatkan SBP dari hasil subscribe pertama kali.",
            ]);
        }
        elseif(file_exists("data/users/$userid.json")){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"Anda bukan pengguna baru.",
            ]);
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$inviter,
                "text"=>"Pengguna $userid melakukan start bot dengan link anda, tapi dia bukan pengguna baru.",
            ]);
        }
        else{
            f("data_save")("inviteref/$userid",$inviter);
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"SELAMAT DATANG!\nAnda telah diundang oleh pengguna $inviter. Ayo dapatkan poin dari channel ".f("get_config")("s4s_channel")." !",
            ]);
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$inviter,
                "text"=>"Pengguna $userid melakukan start bot dengan link anda. Pastikan Anda memiliki minimal 1 SBP saat pengguna baru tersebut mendapatkan SBP dari hasil subscribe pertama kali.",
            ]);
        }
        return true;
    }

    return false;
}