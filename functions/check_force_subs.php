<?php
function check_force_subs($botdata){
    $text = $botdata["text"] ?? "";
    $force_subs = f("get_config")("force_subs",[]);
    $harusjoin = [];
    $user = $botdata['from'];
    $userid = $user['id'];
    foreach($force_subs as $forcesubid){
        $getChatMember = f("bot_kirim_perintah")("getChatMember",[
            'chat_id'=>$forcesubid,
            'user_id'=>$userid,
        ]);
        if(empty($getChatMember["result"]["status"])){
            foreach(f("get_config")("bot_admins",[]) as $chatidadmin){
                f("bot_kirim_perintah")("sendMessage",[
                    'chat_id'=>$chatidadmin,
                    'text'=>"Tolong masukkan saya ke $forcesubid untuk bisa mengecek apakah $userid sudah join/subscribe atau belum.",
                ]);
            };
            file_put_contents("log/Last Error empty status1.txt",print_r([$getChatMember, $user],true));
            die("Error empty status");
        }
        if(in_array($getChatMember["result"]["status"],["restricted","left","kicked"])){
            $chatinfo = f("bot_kirim_perintah")("getChat",[
                "chat_id"=>$forcesubid,
            ]);
            if(!empty($chatinfo['result']['username'])){
                $harusjoin[] = "@" . $chatinfo['result']['username'];
            }
            elseif(!empty($chatinfo['result']['title'])){
                $harusjoin[] = $chatinfo['result']['title'];
            }
            else{
                f("bot_kirim_perintah")("sendMessage",[
                    "chat_id"=>$userid,
                    "text"=>"Maaf, error! \n".print_r($chatinfo,true),
                ]);
                return false;
            }
        }
    }
    if(!empty($harusjoin)){
        $textkirim = "Join ke sini dulu yaa:\n";
        foreach($harusjoin as $item){
            $textkirim .= "➡️ $item\n";
        }
        $textkirim .= "\nAbis itu ke sini lagi.. :D";
        if(f("str_is_diawali")($text,"/start ")){
            $textkirim .= "\nt.me/".f("get_config")("botuname")."?start=".str_replace("/start ","",$text);
        }
        else{
            $textkirim .= "\n/start";
        }
        
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$userid,
            "text"=>$textkirim,
        ]);
        return true;
    }
    
    $botdata["from"]["LAST_ACTIVE"] = date("Y-m-d H:i:s");
    f("data_save")("users/".$botdata["from"]["id"],$user);
    return false;
}