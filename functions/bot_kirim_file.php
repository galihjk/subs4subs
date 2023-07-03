<?php
function bot_kirim_file($chat_id, $filename, $data, $caption){
    $caption = urlencode($caption);
    file_put_contents("temp/$filename",$data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot".f("get_config")("bot_token")."/sendDocument?caption=$caption&chat_id=$chat_id");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    // Create CURLFile
    $finfo = finfo_file(finfo_open(FILEINFO_MIME_TYPE), "temp/$filename");
    $cFile = new CURLFile($filename, $finfo);

    // Add CURLFile to CURL request
    curl_setopt($ch, CURLOPT_POSTFIELDS, [
        "document" => $cFile
    ]);

    // Call
    $result = curl_exec($ch);

    // Show result and close curl
    // var_dump($result);
    curl_close($ch);
    
    return true;
}