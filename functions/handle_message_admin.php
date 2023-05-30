<?php
function handle_message_admin($botdata){
    $user = $botdata['from'];
    $userid = $user['id'];
    $admins = f("get_config")("bot_admins");
    $is_admin = in_array($userid,$admins);
    $text = $botdata["text"] ?? "";
    if($is_admin and f("str_is_diawali")($text,"/broadcast")){
        if(empty($botdata['reply_to_message'])){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$botdata["chat"]["id"],
                "text"=>"Balas suatu pesan dengan command ini, nanti pesan tersebut akan dikirimkan ke semua pengguna",
            ]);
        }
        else{
            $all_user = f("get_all_users")();
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$botdata["chat"]["id"],
                "text"=>"Broadcast dimulai. Harap tunggu sampai selesai.",
            ]);
            $cnt = 0;
            foreach($all_user as $item){
                f("bot_kirim_perintah")("copyMessage",[
                    "chat_id"=>$item,
                    "from_chat_id"=>$botdata['reply_to_message']["chat"]["id"],
                    "message_id"=>$botdata['reply_to_message']["message_id"],
                ]);
                $cnt++;
                usleep(2000 + rand(0,8000));
            }
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$botdata["chat"]["id"],
                "text"=>"Broadcast selesai ($cnt).",
            ]);
            
        }
        return true;
    }
    elseif($is_admin and f("str_is_diawali")($text,"/set_custom_link_")){
        $filepostid = str_replace("/set_custom_link_","",$text);
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$botdata["chat"]["id"],
            "text"=>"Balas pesan ini dengan {link_khusus}. "
                ."Nanti link nya jadi <pre>t.me/".f("get_config")("botuname")."?start={link_khusus}</pre>. "
                ."\n- karakter yg diperbolehkan: huruf, angka, minus (-), underscore (_)"
                ."\n- harus diawali dengan huruf"
                ."\n- max 20 karakter"
                ."\n- jika suda ada, akan ditimpa"
                ."\n\n~$filepostid",
            "parse_mode"=>"HTML",
            'reply_markup' => [
                'force_reply'=>true,
                'input_field_placeholder'=>'Link Khusus',
            ],
        ]);
        return true;
    }
    elseif($is_admin and !empty($botdata['reply_to_message']['text'])
    and f("str_is_diawali")($botdata['reply_to_message']['text'],"Balas pesan ini dengan {link_khusus}.")){
        $explode = explode("~",$botdata['reply_to_message']['text']);
        if(empty($explode[1])){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$botdata["chat"]["id"],
                "text"=>"Pesan tidak valid",
            ]);
            return true;
        }
        $filepostid = $explode[1];
        if(strlen($text) > 20){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$botdata["chat"]["id"],
                "text"=>"Tidak boleh lebih dari 20 karakter",
            ]);
            return true;
        }
        if(preg_match('/[^a-z]/i', substr($text,0,1))){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$botdata["chat"]["id"],
                "text"=>"Harus diawali dengan huruf",
            ]);
            return true;
        }
        if(preg_match('/[^a-z_\-0-9]/i', $text)){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$botdata["chat"]["id"],
                "text"=>"Format {link_khusus} tidak valid",
            ]);
            return true;
        }
        f("data_save")($text,$filepostid);
        $customlink = "t.me/".f("get_config")("botuname")."?start=$text";
        $storage = f("get_config")("storage");
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$storage,
            "text"=>"<b>CUSTOM LINK:</b> $customlink",
            "parse_mode"=>"HTML",
            "disable_web_page_preview"=>true,
            "reply_to_message_id"=>$filepostid,
        ]);
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$botdata["chat"]["id"],
            "text"=>"Berhasil! \n$customlink",
            "parse_mode"=>"HTML",
            "disable_web_page_preview"=>true,
        ]);
        return true;
    }
    return false;
}