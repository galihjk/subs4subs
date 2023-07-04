<?php
function handle_message_admin($botdata){
    $user = $botdata['from'];
    $userid = $user['id'];
    $text = $botdata["text"] ?? "";
    if(
        $botdata["chat"]["id"] == f("get_config")("admin_chat_id","")
        or in_array($userid,f("get_config")("bot_admins",[]))
    ){
        $is_admin = true;
    }
    else{
        $is_admin = false;
    }
    
    if($is_admin and $text == "/admin"){
        $outputtext = "/users \n";
        $outputtext .= "/u_{id} \n";
        $outputtext .= "/add_sbp (spasi) {id} (spasi) {tambahan sbp}\n";
        $outputtext .= "/unban_user (spasi) {id} \n";
        $outputtext .= "/unban_channel (spasi) {channel} \n";
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$botdata["chat"]["id"],
            "text"=>$outputtext,
        ]);
    }
    elseif($is_admin and f("str_is_diawali")($text,"/add_sbp ")){
        $add_sbp = str_replace("@".f("get_config")("botuname"),"",str_replace("/add_sbp ","",$text));
        $explode = explode(" ",$add_sbp);
        if(!empty($explode[0]) and !empty($explode[1])){
            $add_user = $explode[0];
            $add_value = $explode[1];
            $user_sbp = f("get_usersbp")($add_user);
            $user_sbp_awal = $user_sbp;
            $user_sbp += $add_value;
            f("set_usersbp")($add_user,$user_sbp);

            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$botdata["chat"]["id"],
                "text"=>"SBP /u_$add_user diubah dari $user_sbp_awal menjadi $user_sbp oleh /u_$userid",
            ]);
        }
        else{
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>$botdata["chat"]["id"],
                "text"=>"👎",
            ]);
        }
    }
    elseif($is_admin and f("str_is_diawali")($text,"/unban_user ")){
        $unbanuser = str_replace("@".f("get_config")("botuname"),"",str_replace("/unban_user ","",$text));
        unlink("data/banned_users/$unbanuser");
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$botdata["chat"]["id"],
            "text"=>"👍",
        ]);
    }
    elseif($is_admin and f("str_is_diawali")($text,"/unban_channel ")){
        $unban_channel = str_replace("@".f("get_config")("botuname"),"",str_replace("/unban_channel ","",$text));
        unlink("data/banned_channels/$unban_channel");
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$botdata["chat"]["id"],
            "text"=>"👍",
        ]);
    }
    elseif($is_admin and $text == "/users"){
        $datalist = f("data_list")("users");
        // $send_text = "USERS:\n";
        // foreach($datalist as $item){
        //     $send_text .= "/u_$item\n";
        // }
        // f("bot_kirim_perintah")("sendMessage",[
        //     "chat_id"=>$botdata["chat"]["id"],
        //     "text"=>$send_text,
        // ]);
        f("bot_kirim_file")(
            $botdata["chat"]["id"], 
            "users.txt", 
            print_r($datalist,true), 
            "users ".date("Y-m-d-H-i")
        );
        return true;
    }
    elseif($is_admin and f("str_is_diawali")($text,"/u_")){
        $finduserid = str_replace("@".f("get_config")("botuname"),"",substr($text,strlen("/u_")));
        $userdata = f("data_load")("users/$finduserid");
        $usersubsdata = f("data_load")("usersubs/$finduserid");
        
        /*OLD
            $user_addch_history = f("data_load")("user_addch_history/$finduserid");
            $user_addch_history = array_keys($user_addch_history);
        */
        $user_addch_history = f("get_user_addch_history")($finduserid);

        if(!empty($user_addch_history)){
            $usersubsdata['chll'] = [];
            foreach($user_addch_history as $item){
                $msgid = f("data_load")("channelposts/$finduserid-$item",false);
                if($msgid){
                    $usersubsdata['chll'][$item] = f("s4slink")($msgid);
                }
                else{
                    $usersubsdata['chll'][$item] = "-";
                }
            }
        }
        if(file_exists("data/banned_users/$finduserid")){
            $usersubsdata['status'] = "BANNED";
        }
        
        $userdata = array_merge($userdata,$usersubsdata);
        // f("bot_kirim_perintah")("sendMessage",[
        //     "chat_id"=>$botdata["chat"]["id"],
        //     "text"=>print_r($userdata,true),
        //     "disable_web_page_preview"=>true,
        // ]);

        f("bot_kirim_file")(
            $botdata["chat"]["id"], 
            "info_data.txt", 
            print_r($userdata,true), 
            "user $finduserid ".date("Y-m-d-H-i")
        );

        return true;
    }
    return false;
}