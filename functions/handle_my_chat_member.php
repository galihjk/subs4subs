<?php
function handle_my_chat_member($botdata){
    if(!empty($botdata["chat"]["id"])
    and !empty($botdata["from"]["id"])
    and $botdata["from"]["id"] == $botdata["chat"]["id"]
    and !empty($botdata["new_chat_member"]["status"])
    and $botdata["new_chat_member"]["status"] == "kicked"
    ){
        $userdata = f("data_load")("users/$finduserid");
        $userdata['STOP_BOT'] = date("Y-m-d H:i:s");
        f("data_save")("users/".$botdata["from"]["id"],$userdata);
    }
    elseif(!empty($botdata["new_chat_member"]["status"]) 
        and $botdata["new_chat_member"]["status"] == "left"
        and !empty($botdata["new_chat_member"]["user"]["username"]) 
        and $botdata["new_chat_member"]["user"]["username"] == f("get_config")("botuname")
        and !empty($botdata["from"]["id"])
        and !empty($botdata["chat"]["username"])
        and !empty($botdata["chat"]["type"])
        and $botdata["chat"]["type"] == "channel"
    ){
        $usr = $botdata["from"]["id"];
        $channeluname = $botdata["chat"]["username"];
        //check apakah channel itu terdaftar
        $datalist = f("data_list")("channelposts");
        $ada = false;
        foreach($datalist as $item){
            if(f("str_is_diakhiri")($item,"-$channeluname")){
                $ada = true;
                break;
            }
        }
        if($ada){
            //ban user yang nge-kick bot dari channel
            file_put_contents("data/banned_users/$usr", "1");
            f("bot_kirim_perintah")("sendMessage",[
                'chat_id'=>$usr,
                'text'=>"Anda telah mengeluarkan bot dari channel @$channeluname. Anda dan channel ini di-banned.",
            ]);
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>f("get_config")("log_chat_id"),
                "text"=>"/u_$usr telah mengeluarkan bot dari channel @$channeluname\nUser dan channel tersebut di-banned.",
            ]);
            //ban channel
            file_put_contents("data/banned_channels/$channeluname", "1");
            $userchannelpost = f("data_load")("channelposts/$usr-$channeluname");
            if(!empty($userchannelpost)){
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
                f("data_delete")("channelposts/$usr-$channeluname");
            }
        }
    }
}