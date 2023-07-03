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
    if($is_admin and $text == "/users"){
        $datalist = f("data_list")("users");
        $send_text = "USERS:\n";
        foreach($datalist as $item){
            $send_text .= "/u_$item\n";
        }
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$botdata["chat"]["id"],
            "text"=>$send_text,
        ]);
        return true;
    }
    elseif($is_admin and f("str_is_diawali")($text,"/u_")){
        $finduserid = str_replace("@".f("get_config")("botuname"),"",substr($text,strlen("/u_")));
        $userdata = f("data_load")("users/$finduserid");
        $usersubsdata = f("data_load")("usersubs/$finduserid");
        $user_addch_history = f("data_load")("user_addch_history/$finduserid");
        if(!empty($user_addch_history)){
            $user_addch_history = array_keys($user_addch_history);
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

        //============================
        file_put_contents("info_data.txt",print_r($userdata,true));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot".f("get_config")("bot_token")."/sendDocument?caption=user+$finduserid+".date("Y-m-d-H-i")."&chat_id=" . $botdata["chat"]["id"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
    
        // Create CURLFile
        $finfo = finfo_file(finfo_open(FILEINFO_MIME_TYPE), "info_data.txt");
        $cFile = new CURLFile("info_data.txt", $finfo);
    
        // Add CURLFile to CURL request
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            "document" => $cFile
        ]);
    
        // Call
        $result = curl_exec($ch);
    
        // Show result and close curl
        // var_dump($result);
        curl_close($ch);
        //============================

        return true;
    }
    return false;
}