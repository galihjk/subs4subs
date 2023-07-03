<?php
function check_unsubscribe_user($user = "{ALL_USERS}"){
    $current_time = time();
    $return = "";

    if($user === "{ALL_USERS}"){
        $all_users = f("data_list")("users");
    }
    else{
        $all_users = [$user];
    }
    
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
            continue;
        }
        $cnt = $cnt['result'];
        if($sbcount != $cnt){
            $channels_berubah[]=$chname;
            $return .=  "$chname berubah dari ".$channels[$item]['cnt']." menjadi $cnt\n";
            f("data_save")("channelsbcount/$chname",$cnt);
        }
        else{
            $return .=  "$chname tidak berubah ($cnt)\n";
        }
    }
    
    $return .=  "\n---channels_berubah--\n";
    $return .= print_r($channels_berubah, true);

    $return .=  "\n-----\n";
    foreach($users_check as $usr=>$usrchs){
        $return .=  "$usr: ";
        $usr_checksubs = [];
        $usersbp = f("get_usersbp")($usr);
        $newusersubs = $usrchs;
        $unsubscribe = false;
        $ban = false;
        $admin_info = "";
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
                        f("bot_kirim_perintah")("sendMessage",[
                            'chat_id'=>$usr,
                            'text'=>"Anda berhasil unsubscribe banned channel @$usrchs",
                        ]);
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
            $addch_history = f("data_load")("user_addch_history/$usr",[]);
            $addch_history = array_keys($addch_history);
            if(!empty($addch_history)){
                $outputtext = "WARNING!\nPengguna $usr telah melakukan unsubscribe, ia dan channelnya di-banned.\nSilakan unsubscribe channel berikut:\n";
                foreach($addch_history as $item_addchh){
                    file_put_contents("data/banned_channels/$item_addchh", "1");
                    $return .=  "Channel $item_addchh BANNED!\n";
                    $outputtext .= "@$item_addchh\n";
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
                f("bot_kirim_perintah")("sendMessage",[
                    "chat_id"=>f("get_config")("s4s_channel"),
                    "text"=>$outputtext,
                    // "parse_mode"=>"HTML",
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
                            $outputtext .= "@$item_uns\n";
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

    return $return;
}