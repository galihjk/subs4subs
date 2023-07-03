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