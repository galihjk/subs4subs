<?php
function str_encrypt($str, $urlencode = false){
    $ciphering = "AES-128-CTR";
    $iv_length = openssl_cipher_iv_length($ciphering);
    $options = 0;
    $encryption_iv = '1234567891101121';
    $encryption_key = f("get_config")("bot_token","");
    $encryption = openssl_encrypt(
        $str, $ciphering,
        $encryption_key, $options, $encryption_iv
    );
    if($urlencode) $encryption = urlencode($encryption);
    return $encryption;
}