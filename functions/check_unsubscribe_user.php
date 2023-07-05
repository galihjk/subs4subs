<?php
function check_unsubscribe_user($user = "{ALL_USERS}"){
    f("data_save")("job_is_running",1);
    $microtime_start = microtime(true);
    $current_time = time();
    $return = "";

    if($user === "{ALL_USERS}"){
        $all_users = f("data_list")("users");
    }
    else{
        $all_users = [$user];
    }
    $admin_info = "";
    
    $usertime = [];
    foreach($all_users as $item){
        $usertime[$item] = f("data_time")("usersubs/$item");
    }
    asort($usertime);
    $return .=  "\n---usertime--\n";
    $return .= print_r($usertime, true);
    $max_check_all_user_subs = f("get_config")("max_check_all_user_subs");
    $detik_check_subs_all_users = f("get_config")("detik_check_subs_all_users");
    $return .=  "max_check_all_user_subs=$max_check_all_user_subs, detik_check_subs_all_users=$detik_check_subs_all_users\n";
    $loopcount = 1;
    $users_check = [];
    foreach($usertime as $usr=>$usr_t){
        if($loopcount > $max_check_all_user_subs){
            $return .=  "User berikutnya ($loopcount s.d. ". count($usertime) .") akan dicek pada job berikutnya.\n";
            break;
        }
        $usersubs = f("get_usersubs")($usr);
        if($current_time - $usr_t > $detik_check_subs_all_users){
            $users_check[$usr] = $usersubs;
            $return .=  "V] $usr perlu dicek (".$current_time."-".$usr_t." = ". ($current_time - $usr_t) ." > $detik_check_subs_all_users)\n";
        }
        else{
            $return .=  "X] $usr tidak perlu dicek (".$current_time."-".$usr_t." = ". ($current_time - $usr_t) .")\n";
        }
        f("set_usersubs")($usr,$usersubs);
        $loopcount++;
    }
    $return .=  "\n---users_check--\n";
    $return .= print_r($users_check, true);

    $channels = [];
    foreach($users_check as $usrchs){
        $channels = array_unique(array_merge($channels, $usrchs));
    }
    $return .=  "\n---channels--\n";
    $return .= print_r($channels, true);

    $channelsbcount = [];
    foreach($channels as $item){
        $channelsbcount[$item] = f("data_load")("channelsbcount/$item",0);
    }

    $channels_berubah = [];
    foreach($channelsbcount as $chname=>$sbcount){
        $cnt = f("bot_kirim_perintah")("getChatMemberCount",[
            'chat_id'=>"@$chname",
        ]);
        if(empty($cnt['ok'])){
            $return .=  "ERROR! jumlah subscriber $chname tidak bisa didapatkan.\n";
            $admin_info .= "Jumlah subscriber @$chname tidak bisa didapatkan oleh bot\n";
            $channels_berubah[]=$chname;
            continue;
        }
        $cnt = $cnt['result'];
        if($sbcount != $cnt){
            $channels_berubah[]=$chname;
            $return .=  "$chname berubah dari ".$sbcount." menjadi $cnt\n";
            f("data_save")("channelsbcount/$chname",$cnt);
        }
        else{
            $return .=  "$chname tidak berubah ($cnt)\n";
        }
    }
    
    $return .=  "\n---channels_berubah--\n";
    $return .= print_r($channels_berubah, true);

    $channeltime = [];
    foreach($channels_berubah as $item){
        $channeltime[$item] = f("data_time")("channels/$item");
    }
    asort($channeltime);
    $detik_check_channeluname = f("get_config")("detik_check_channeluname");
    $return .=  "detik_check_channeluname=$detik_check_channeluname\n";
    $check_channel_uname = [];
    foreach($channeltime as $k_ch=>$v_time){
        if($current_time - $v_time > $detik_check_channeluname){
            $check_channel_uname[$k_ch] = f("data_load")("channels/$k_ch",["id" => "-0"]);
            $return .=  "V] $k_ch perlu dicek usernamenya(".$current_time."-".$v_time." = ". ($current_time - $v_time) ." > $detik_check_channeluname)\n";
        }
        else{
            $return .=  "X] $k_ch tidak perlu dicek usernamenya(".$current_time."-".$v_time." = ". ($current_time - $v_time) .")\n";
        }
    }
    $return .=  "\n---check_channel_uname--\n";
    $return .= print_r($check_channel_uname, true);
    $channel_ganti_uname = [];
    foreach($check_channel_uname as $k_ch=>$item){
        $chid = $item["id"] ?? "[unknown]";
        $chatinfo = f("bot_kirim_perintah")("getChat",[
            "chat_id"=>"@$k_ch",
        ]);
        if(!empty($chatinfo['result'])){
            f("data_save")("channels/$k_ch",$chatinfo['result']);
        }
        else{
            //gagal mendapatkan ID
            $chatinfo['result']["id"] = "(unknown)";
        }
        if($chatinfo['result']["id"] != $chid){
            $chatinfo = f("bot_kirim_perintah")("getChat",[
                "chat_id"=>$chid,
            ]);
            $username_baru = "unknown";
            if($chatinfo['result']["id"] != "(unknown)" and !empty($chatinfo['result']["username"])){
                $username_baru = "@".$chatinfo['result']["username"];
                file_put_contents("data/banned_channels/".$chatinfo['result']["username"], "1");
                $chowner = false;
                $datalist = f("data_list")("channelposts");
                foreach($datalist as $item){
                    if(f("str_is_diakhiri")($item, "-".$chatinfo['result']["username"])){
                        $chowner = str_replace("-".$chatinfo['result']["username"],"",$item);
                        break;
                    }
                }
                if($chowner){
                    $userchannelpost = f("data_load")("channelposts/$chowner-".$chatinfo['result']["username"]);
                    if(!empty($userchannelpost)){
                        $deleteMsg = f("bot_kirim_perintah")('deleteMessage',[
                            'chat_id' => f("get_config")("s4s_channel"),
                            'message_id' => $userchannelpost,
                        ]);
                        if(empty($deleteMsg['ok'])){
                            f("bot_kirim_perintah")('editMessageText',[
                                'chat_id' => f("get_config")("s4s_channel"),
                                'text'=> "This message has been #deleted",
                                'parse_mode'=>'HTML',
                                'message_id' => $userchannelpost,
                            ]);
                        }
                        f("data_delete")("channelposts/$chowner-".$chatinfo['result']["username"]);
                    }
                }
            }
            $channel_ganti_uname[$k_ch] = $username_baru;
            $admin_info .= "Channel @$k_ch berubah dari $chid jadi $username_baru ".$chatinfo['result']["id"]."\nChannel ini dibanned!\n";
            $return .= "Channel @$k_ch berubah dari $chid jadi $username_baru ".$chatinfo['result']["id"]."\nChannel ini dibanned!\n";
            file_put_contents("data/banned_channels/$k_ch", "1");
            $datalist = f("data_list")("channelposts");
            $chowner = false;
            foreach($datalist as $item){
                if(f("str_is_diakhiri")($item, "-$k_ch")){
                    $chowner = str_replace("-$k_ch","",$item);
                    break;
                }
            }
            if($chowner){
                $userchannelpost = f("data_load")("channelposts/$chowner-$k_ch");
                if(!empty($userchannelpost)){
                    $deleteMsg = f("bot_kirim_perintah")('deleteMessage',[
                        'chat_id' => f("get_config")("s4s_channel"),
                        'message_id' => $userchannelpost,
                    ]);
                    if(empty($deleteMsg['ok'])){
                        f("bot_kirim_perintah")('editMessageText',[
                            'chat_id' => f("get_config")("s4s_channel"),
                            'text'=> "This message has been #deleted",
                            'parse_mode'=>'HTML',
                            'message_id' => $userchannelpost,
                        ]);
                    }
                    f("data_delete")("channelposts/$chowner-$k_ch");
                }
                // tidak perlu, sekalian aja pakai user_addch_history
                // f("bot_kirim_perintah")("sendMessage",[
                //     'chat_id'=>$chowner,
                //     'text'=>"Channel @$k_ch telah di-banned karena telah berganti username",
                // ]);
            }
            
            $list_user_addch_history = f("data_list")("user_addch_history");
            $ch_check = "-".$k_ch;
            $chadmins = [];
            foreach($list_user_addch_history as $item){
                if(f("str_is_diakhiri")($item,$ch_check)){
                    $chadmins[substr($item,0,strlen($item)-strlen($ch_check))] = true;
                }
                if(!empty($chatinfo['result']["username"]) 
                and f("str_is_diakhiri")($item,"-".$chatinfo['result']["username"])){
                    $chadmins[substr($item,0,strlen($item)-strlen("-".$chatinfo['result']["username"]))] = true;
                }
            }
            $chadmins = array_keys($chadmins); 
            foreach($chadmins as $item){
                file_put_contents("data/banned_users/$item", "1");
                $admin_info .= "User /u_$item dibanned sebagai admin Channel @$k_ch ($username_baru)\n";
                $return .= "User $item dibanned sebagai admin Channel @$k_ch\n ($username_baru)";
                f("bot_kirim_perintah")("sendMessage",[
                    'chat_id'=>$item,
                    'text'=>"Anda dan Channel @$k_ch ($username_baru) telah di-banned karena channel tersebut telah berganti username",
                ]);
            }
        }
    }


    $return .=  "\n-----\n";
    foreach($users_check as $usr=>$usrchs){
        $return .=  "$usr: ";
        $usr_checksubs = [];
        $usersbp = f("get_usersbp")($usr);
        $newusersubs = $usrchs;
        $unsubscribe = false;
        $ban = false;
        foreach($usrchs as $k=>$item_usrch){
            $result = "";
            if(in_array($item_usrch, $channels_berubah)){
                $getChatMember = f("bot_kirim_perintah")("getChatMember",[
                    'chat_id'=>"@$item_usrch",
                    'user_id'=>$usr,
                ]);
                if(empty($getChatMember["result"]["status"])
                or in_array($getChatMember["result"]["status"],["restricted","left","kicked"])
                ){
                    if(file_exists("data/banned_channels/$item_usrch")){
                        if(!empty($channel_ganti_uname[$item_usrch])){
                            f("bot_kirim_perintah")("sendMessage",[
                                'chat_id'=>$usr,
                                'text'=>"Channel @$item_usrch (".$channel_ganti_uname[$item_usrch].") telah dibanned. Silakan lakukan unsubscribe.",
                            ]);
                        }
                        else{
                            f("bot_kirim_perintah")("sendMessage",[
                                'chat_id'=>$usr,
                                'text'=>"Anda berhasil unsubscribe banned channel @$item_usrch",
                            ]);
                        }
                        $result = "unsubscribe banned channel";
                    }
                    else{
                        f("bot_kirim_perintah")("sendMessage",[
                            'chat_id'=>$usr,
                            'text'=>"Anda telah unsubscribe @$item_usrch, Anda dan channel Anda di-banned.",
                        ]);
                        $ban = true;
                        $result = "UNSUBSCRIBE!!";
                    }
                    $unsubscribe = true;
                    unset($newusersubs[$k]);
                    $usersbp--;
                    $admin_info .= "/u_$usr unsubscribe @$item_usrch\n";
                }
                else{
                    $result = "subscribed";
                }
            }
            else{
                $result = "no check";
            }
            $usr_checksubs[$item_usrch] = $result;
        }
        $return .= print_r($usr_checksubs, true);
        $return .=  "\n";
        
        if($unsubscribe){
            f("set_usersb")($usr,[
                'SBP' => $usersbp,
                'subs' => array_values($newusersubs),
            ]);
        }
        if($ban){
            $admin_info .= "/u_$usr di-banned.\n";
            file_put_contents("data/banned_users/$usr", "1");
            $return .=  "User $usr BANNED!\n";
            /*OLD
                $addch_history = f("data_load")("user_addch_history/$usr",[]);
                $addch_history = array_keys($addch_history);
            */
            $addch_history = f("get_user_addch_history")($usr);
            
            if(!empty($addch_history)){
                $outputtext = "WARNING!\nPengguna $usr telah melakukan unsubscribe, silakan unsubscribe juga channelnya:\n";
                $channelids = [];
                foreach($addch_history as $item_addchh){
                    $chdata = f("data_load")("channels/$item_addchh");
                    if(!empty($chdata["id"])){
                        $channelid = $chdata["id"];
                    }
                    else{
                        $channelid = "";
                    }
                    $channelids[$item_addchh] = $channelid;
                    file_put_contents("data/banned_channels/$item_addchh", "1");
                    $return .=  "Channel $item_addchh BANNED!\n";
                    $outputtext .= "@$item_addchh ($channelid)\n";
                    $admin_info .= "Channel @$item_addchh di-banned.\n";
                    $userchannelpost = f("data_load")("channelposts/$usr-$item_addchh");
                    if(!empty($userchannelpost)){
                        $deleteMsg = f("bot_kirim_perintah")('deleteMessage',[
                            'chat_id' => f("get_config")("s4s_channel"),
                            'message_id' => $userchannelpost,
                        ]);
                        if(empty($deleteMsg['ok'])){
                            f("bot_kirim_perintah")('editMessageText',[
                                'chat_id' => f("get_config")("s4s_channel"),
                                'text'=> "This message has been #deleted",
                                'parse_mode'=>'HTML',
                                'message_id' => $userchannelpost,
                            ]);
                        }
                        f("data_delete")("channelposts/$usr-$item_addchh");
                    }
                }
                $outputtext .= "\n<i>*the user and his channels have been banned</i>";
                f("bot_kirim_perintah")("sendMessage",[
                    "chat_id"=>f("get_config")("s4s_channel"),
                    "text"=>$outputtext,
                    "parse_mode"=>"HTML",
                ]);
                //info ke user
                $all_users = f("data_list")("users");
                foreach($all_users as $item){
                    $usersubs = f("get_usersubs")($item);
                    $user_ch_should_unsubs = [];
                    foreach($addch_history as $item_addchh){
                        if(in_array($item_addchh,$usersubs)){
                            $user_ch_should_unsubs[] = $item_addch;
                        }
                    }
                    if(!empty($user_ch_should_unsubs)){
                        $outputtext = "Silakan unsubscribe channel berikut karena telah dibanned:\n";
                        foreach($user_ch_should_unsubs as $item_uns){
                            $channelid = $channelids[$item_uns];
                            $linkchannel = "t.me/c/".str_replace("-100","",$channelid)."/1";
                            $outputtext .= "@$item_uns ($channelid) <a href='$linkchannel'>[↗️]</a>\n";
                        }
                        f("bot_kirim_perintah")("sendMessage",[
                            "chat_id"=>$item,
                            "text"=>$outputtext,
                            // "parse_mode"=>"HTML",
                        ]);
                    }
                }
            }
        }
        if(!empty($admin_info)){
            f("bot_kirim_perintah")("sendMessage",[
                "chat_id"=>f("get_config")("log_chat_id"),
                "text"=>$admin_info,
                // "parse_mode"=>"HTML",
            ]);
        }
    }
    
    /*
    $usersubs = f("get_usersubs")($userid);
    $usersbp = f("get_usersbp")($userid);
    $newusersubs = $usersubs;
    $unsubscribe = false;
    foreach($usersubs as $k=>$channel){
        $getChatMember = f("bot_kirim_perintah")("getChatMember",[
            'chat_id'=>"@$channel",
            'user_id'=>$userid,
        ]);
        if(empty($getChatMember["result"]["status"])
        or in_array($getChatMember["result"]["status"],["restricted","left","kicked"])
        ){
            f("bot_kirim_perintah")("sendMessage",[
                'chat_id'=>$userid,
                'text'=>"Anda telah unsubscribe @$channel, SBP -1",
            ]);
            $unsubscribe = true;
            unset($newusersubs[$k]);
            $usersbp--;
        }
    }
    if($unsubscribe){
        f("set_usersb")($userid,[
            'SBP' => $usersbp,
            'subs' => array_values($newusersubs),
        ]);
    }
    */
    f("data_delete")("job_is_running");
    $exectime = microtime(true) - $microtime_start;
    $return .= "\nexectime=$exectime\n";

    return $return;
}