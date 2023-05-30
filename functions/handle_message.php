<?php
function handle_message($botdata){
    $chat_id = $botdata["chat"]["id"];
    f("handle_botdata_functions")($botdata,[
        "check_force_subs",
        "check_unsubscribe",
        "check_unauthorized_group",
        "handle_message_msgcmd",
        "handle_message_admin",
        "handle_message_storefile",
        "handle_message_upload",
        "handle_message_getfile",
        "handle_message_others",
    ]);
}