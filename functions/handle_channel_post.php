<?php
function handle_channel_post($botdata){
    $chat_id = $botdata["chat"]["id"];
    f("handle_botdata_functions")($botdata,[
        "handle_channel_post_id",
    ]);
}