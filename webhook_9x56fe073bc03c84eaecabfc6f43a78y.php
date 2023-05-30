<?php
include("init.php");

$jenis_update = [
    "message",
    "callback_query",
    "my_chat_member",
    "channel_post",
];

f("handle_update_sesuai_jenis")($jenis_update);

// f("db_disconnet")();
