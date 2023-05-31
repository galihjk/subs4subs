<?php function handle_message_add_channel($botdata){
        $chatid = $botdata['chat']['id']; if(empty($chatid)) return false;
        $userid = $botdata['from']['id']; if(empty($userid)) return false;
        $text = $botdata["text"] ?? ""; if(empty($text)) return false;
    
        if($text =="/add_channel"){
            if(f("check_menunggu_persetujuan")($userid)) return true;
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$chatid,
                "text"=>"Balas pesan ini dengan mention channel Anda!\nAwali dengan tanda \"@\"\nContoh: ".f("get_config")("s4s_channel"),
                "parse_mode"=>"HTML",
                "disable_web_page_preview"=>true,
                'reply_markup' => [
                    'force_reply'=>true,
                    'input_field_placeholder'=>'@channel_anda',
                    'selective'=>true,
                ],
            ]);
            return true;
        }
        elseif(!empty($botdata['reply_to_message']['text'])
        and f("str_contains")($botdata['reply_to_message']['text'], "Balas pesan ini dengan mention channel Anda!")){
            if(f("check_menunggu_persetujuan")($userid)) return true;
            if(!f("str_is_diawali")($text,"@")){
                f("bot_kirim_perintah")("sendMessage",[
                    "chat_id"=>$chatid,
                    "text"=>"Gagal: channel harus diawali \"@\"",
                    "parse_mode"=>"HTML",
                    "disable_web_page_preview"=>true,
                    'reply_markup' => [
                        'force_reply'=>false,
                    ],
                ]);
                return true;
            }
            $sbp = f("get_usersbp")($userid);
            if($sbp < 1){
                f("bot_kirim_perintah")("sendMessage",[
                    "chat_id"=>$chatid,
                    "text"=>"Gagal: Anda harus memiliki minimal 1 SBP \n/help",
                    "parse_mode"=>"HTML",
                    "disable_web_page_preview"=>true,
                    'reply_markup' => [
                        'force_reply'=>false,
                    ],
                ]);
                return true;
            }
            $channel_already_exists = f("check_channel_already_exists")($text);
            if($channel_already_exists){
                $linktopost = f("s4slink")(f("data_load")("channelposts/$channel_already_exists",1));
                f("bot_kirim_perintah")("sendMessage",[
                    "chat_id"=>$chatid,
                    "text"=>"Gagal: Channel ini sudah ada. <a href='$linktopost'>[lihat]</a>",
                    "parse_mode"=>"HTML",
                    "disable_web_page_preview"=>true,
                    'reply_markup' => [
                        'force_reply'=>false,
                    ],
                ]);
                return true;
            }
            $getChatMember = f("bot_kirim_perintah")("getChatMember",[
                'chat_id'=>$text,
                'user_id'=>$userid,
            ]);
            if(empty($getChatMember["result"]["status"])){
                f("bot_kirim_perintah")("sendMessage",[
                    "chat_id"=>$chatid,
                    "text"=>"Gagal: Bot harus ditambahkan dahulu sebagai admin di channel $text",
                    "parse_mode"=>"HTML",
                    "disable_web_page_preview"=>true,
                    'reply_markup' => [
                        'force_reply'=>false,
                    ],
                ]);
                return true;
            }
            if(!in_array($getChatMember["result"]["status"],["owner","administrator"])){
                f("bot_kirim_perintah")("sendMessage",[
                    "chat_id"=>$chatid,
                    "text"=>"Gagal: Anda harus termasuk admin channel $text\nStatus Anda: "
                    . print_r($getChatMember,true)
                    . $getChatMember["result"]["status"] ,
                    "parse_mode"=>"HTML",
                    "disable_web_page_preview"=>true,
                    'reply_markup' => [
                        'force_reply'=>false,
                    ],
                ]);
                return true;
            }
            $channelpost = f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>f("get_config")("admin_chat_id"),
                "text"=>"/u_$userid ingin menambahkan channel $text\n"
                    ."/setuju_$userid\n"
                    ."/tolak_$userid\n",
                "parse_mode"=>"HTML",
                'reply_markup' => [
                    'force_reply'=>false,
                ],
            ]);
            f("data_save")("waiting_confirmation/$userid",substr($text, 1));
        }
        elseif($chatid == f("get_config")("admin_chat_id") and f("str_is_diawali")($text,"/tolak_")){
            $requester = substr($text,strlen("/tolak_"));
            $channel_confirmation = f("data_load")("waiting_confirmation/$requester" , false);
            if(!$channel_confirmation){
                f("bot_kirim_perintah")("sendMessage",[
                    "chat_id"=>$chatid,
                    "text"=>"Ini sudah tidak dapat diproses",
                    "parse_mode"=>"HTML",
                    "reply_to_message_id"=>$botdata['message_id'],
                ]);
            }
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$requester,
                "text"=>"Penambahan channel @$channel_confirmation ditolak oleh admin",
                "parse_mode"=>"HTML",
            ]);
            f("data_delete")("waiting_confirmation/$requester");
        }
        return false;
}