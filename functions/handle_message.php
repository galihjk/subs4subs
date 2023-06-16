<?php
function handle_message($botdata){
    $chat_id = $botdata["chat"]["id"];
    f("handle_botdata_functions")($botdata,[
        "check_banned_user",
        "check_force_subs",
        "check_unsubscribe",
        "check_unauthorized_group",
        "handle_message_msgcmd",
        "handle_message_my_channel",
        "handle_message_hapus_channel",
        "handle_message_my_sbp",
        "handle_message_add_channel",
        "handle_message_get_sbp",
        "handle_message_admin",
        "handle_message_others",
    ]);
}