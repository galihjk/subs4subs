<?php 

function bot_kirim_perintah($perintah,$data,$bot_token = "default"){
    if($bot_token == "default"){
        $bot_token = f("get_config")("bot_token");
    }

	if(empty($data)){
		$data = [];
	}

	//set data yang berupa array menjadi json agar sesuai dengan api bot telegram 
	foreach($data as $key => $val){
		if(is_array($val)){
			$data[$key] = json_encode($val);
		}
	}

    // Detek otomatis metode curl atau stream 
    if(is_callable('curl_init')) {
        $hasil = bot_kirim_curl($bot_token,$perintah,$data);
        //cek kembali, terkadang di XAMPP Curl sudah aktif
        //namun pesan tetap tidak terikirm, maka kita tetap gunakan Stream
        if (empty($hasil)){
            $hasil = bot_kirim_stream($bot_token,$perintah,$data);
        }   
    } else {
        $hasil = bot_kirim_stream($bot_token,$perintah,$data);
    }	

    //santuy
    if (!empty($hasil) and ($perintah == "sendMessage" or $perintah == "editMessageText")) usleep(30000);
    
    //debug
    $debug = json_decode($hasil,true);

	//kalau gagal kirim ke suatu chat id, non aktifkan
	if(empty($debug['ok']) and !empty($data['chat_id'])){
		//under
	}
	
    //laporan error
    if(!$debug['ok']){
		file_put_contents("last_error.txt", print_r([$perintah,$data,$bot_token],true)."\n\n".print_r($debug,true));
        print_r($debug);
    }
    
    return $debug;  
}

//Fungsi untuk Penyederhanaan kirim perintah dari URI API Telegram
function bot_url_kirim($bot_token,$perintah){
    return 'https://api.telegram.org/bot'.$bot_token.'/'.$perintah;
}
  
//Fungsi untuk mengirim "perintah" ke Telegram
function bot_kirim_stream($bot_token,$perintah,$data){
    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );
    $context  = stream_context_create($options);
    $result = file_get_contents(bot_url_kirim($bot_token,$perintah), false, $context);
    return $result;
}

function bot_kirim_curl($bot_token,$perintah,$data){
	if(empty($data)) $data = [];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,bot_url_kirim($bot_token,$perintah));
    curl_setopt($ch, CURLOPT_POST, count($data));
    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $kembali = curl_exec ($ch);
    curl_close ($ch);

    return $kembali;
}
