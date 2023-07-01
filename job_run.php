<?php
include("init.php");
$last_job_run = f("data_load")("last_job_run",0);
$current_time = time();
echo "<h1>Running job [$current_time]</h1>";
if($last_job_run != $current_time){
    f("data_save")("last_job_run",$current_time);

    echo "<pre>";
    echo "current_time=$current_time - last_job_run=$last_job_run ==== ".($current_time-$last_job_run)."\n";
    

    //get user list by date ascending
    $all_users = f("data_list")("users");
    
    $usertime = [];
    foreach($all_users as $item){
        $usertime[$item] = f("data_time")("usersubs/$item");
    }
    asort($usertime);
    echo "\n---usertime--\n";
    print_r($usertime);
    $max_check_all_user_subs = f("get_config")("max_check_all_user_subs");
    $detik_check_subs_all_users = f("get_config")("detik_check_subs_all_users");
    echo "max_check_all_user_subs=$max_check_all_user_subs, detik_check_subs_all_users=$detik_check_subs_all_users\n";
    $loopcount = 1;
    $users_check = [];
    foreach($usertime as $usr=>$usr_t){
        if($loopcount > $max_check_all_user_subs){
            echo "User berikutnya ($loopcount s.d. ". count($usertime) .") akan dicek pada job berikutnya.\n";
            break;
        }
        $usersubs = f("get_usersubs")($usr);
        if($current_time - $usr_t > $detik_check_subs_all_users){
            $users_check[$usr] = $usersubs;
            echo "V] $usr perlu dicek (".$current_time."-".$usr_t." = ". ($current_time - $usr_t) ." > $detik_check_subs_all_users)\n";
        }
        else{
            echo "X] $usr tidak perlu dicek (".$current_time."-".$usr_t." = ". ($current_time - $usr_t) .")\n";
        }
        f("set_usersubs")($usr,$usersubs);
        $loopcount++;
    }
    echo "\n---users_check--\n";
    print_r($users_check);

    $channels = [];
    foreach($users_check as $usrchs){
        $channels = array_unique(array_merge($channels, $usrchs));
    }
    echo "\n---channels--\n";
    print_r($channels);

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
            echo "ERROR! jumlah subscriber $chname tidak bisa didapatkan.\n";
            continue;
        }
        $cnt = $cnt['result'];
        if($sbcount != $cnt){
            $channels_berubah[]=$chname;
            echo "$chname berubah dari ".$channels[$item]['cnt']." menjadi $cnt\n";
            f("data_save")("channelsbcount/$chname",$cnt);
        }
        else{
            echo "$chname tidak berubah ($cnt)\n";
        }
    }
    
    echo "\n---channels_berubah--\n";
    print_r($channels_berubah);

    echo "\n-----\n";
    foreach($users_check as $usr=>$usrchs){
        echo "$usr: ";
        $usr_checksubs = [];
        foreach($usrchs as $item_usrch){
            $result = "";
            if(in_array($item_usrch, $channels_berubah)){
                $result = "subscribed";
            }
            else{
                $result = "no check";
            }
            $usr_checksubs[$item_usrch] = $result;
        }
        print_r($usr_checksubs);
        echo "\n";
    }
    
}
else{
    echo "job $current_time has been already executed";
}