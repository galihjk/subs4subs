<?php
function check_sbp_habis($userid){
    $user_sbp = f("get_usersbp")($userid);
    if($user_sbp < 1){
        $output_text = "SBP Anda habis, Postingan ke channel berikut telah dihapus:\n";
        $userchannels = f("get_userchannels")($userid);
        foreach($userchannels as $channel){
            $output_text .= "- $channel\n";
            $msgid = f("data_load")("channelposts/$userid-$channel");
            if(empty($msgid)){
                $output_text .= "ERROR channelposts/$userid-$channel is empty\n";
                continue;
            }
            $deleteMsg = f("bot_kirim_perintah")('deleteMessage',[
                'chat_id' => f("get_config")("s4s_channel"),
                'message_id' => $msgid,
            ]);
            if(empty($deleteMsg['ok'])){
                f("bot_kirim_perintah")('editMessageText',[
                    'chat_id' => f("get_config")("s4s_channel"),
                    'text'=> "This message has been #deleted",
                    'parse_mode'=>'HTML',
                    'message_id' => $msgid,
                ]);
            }
            f("data_delete")("channelposts/$userid-$channel");
        }
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$userid,
            "text"=>$output_text,
            "parse_mode"=>"HTML",
            "disable_web_page_preview"=>true,
        ]);
        return true;
    }
    return false;
}