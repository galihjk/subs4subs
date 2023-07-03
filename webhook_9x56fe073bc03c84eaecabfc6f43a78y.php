<?php
include("init.php");

// ----------------------------------------------------------------
// debug
// $update = json_decode(file_get_contents("php://input"), TRUE);
// foreach(f("get_config")("bot_admins",[]) as $chatidadmin){
//     f("bot_kirim_perintah")("sendMessage",[
//         'chat_id'=>$chatidadmin,
//         'text'=>"-".print_r($update,true),
//     ]);
// };
// die();
//----------------------------------------------------------------

$jenis_update = [
    "message",
    "callback_query",
    "my_chat_member",
    "channel_post",
];

f("handle_update_sesuai_jenis")($jenis_update);

// f("db_disconnet")();
