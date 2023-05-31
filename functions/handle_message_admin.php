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
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$botdata["chat"]["id"],
            "text"=>"underconst u $text",
        ]);
        return true;
    }
    elseif($is_admin and $text == "asd"){
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$botdata["chat"]["id"],
            "text"=>f("s4slink")(1),
        ]);
        return true;
    }
    return false;
}