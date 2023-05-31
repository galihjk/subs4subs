<?php
function check_menunggu_persetujuan($userid){
    $menunggu_persetujuan = f("data_load")("waiting_confirmation/$userid" , false);
    if($menunggu_persetujuan){
        f("bot_kirim_perintah")("sendMessage",[
            "chat_id"=>$userid,
            "text"=>"Saat ini Anda sedang dalam proses menunggu persetujuan admin untuk menambahkan channel @$menunggu_persetujuan",
            "parse_mode"=>"HTML",
            "disable_web_page_preview"=>true,
        ]);
    }
    return $menunggu_persetujuan;
}