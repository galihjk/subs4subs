<?php
function handle_message_others($botdata){
    // $is_admin = in_array($botdata['from']['id'],f("get_config")("bot_admins",[]));
    f("bot_kirim_perintah")("sendMessage",[
        "chat_id"=>$botdata["chat"]["id"],
        "text"=>"Perintah tidak dipahami. Gunakan /start",
    ]);
    return true;
}