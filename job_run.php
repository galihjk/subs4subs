<?php
include("init.php");
$last_job_run = f("data_load")("last_job_run",0);
$current_time = time();
echo "<h1>Running job [$current_time]</h1>";
if($last_job_run != $current_time){
    f("data_save")("last_job_run",$current_time);
    $datalist = f("data_list")("channelposts");
    $channels = [];
    foreach($datalist as $item){
        $chname = explode("-",$item)[1];
        $cnt = f("data_load")("channelsbcount/$chname",0);
        $time = f("data_time")("channelsbcount/$chname",0);
        $channels[$chname] = [
            'cnt' => $cnt,
            'time' => $time,
        ];
    }
    echo "<pre>";
    echo "current_time=$current_time - last_job_run=$last_job_run\n";
    echo "\n-----\n";
    print_r($channels);
    echo "\n-----\n";
    $time = time();
    $detik_check_channelsbcount = f("get_config")("detik_check_channelsbcount");
    echo "time=$time, detik_check_channelsbcount=$detik_check_channelsbcount\n";
    $channels_check = [];
    foreach($channels as $k=>$item){
        if($time - $item['time'] > $detik_check_channelsbcount){
            $channels_check[] = $k;
            echo "V] $k perlu dicek jumlahnya (".$time."-".$item['time']." = ". ($time - $item['time']) ." > $detik_check_channelsbcount)\n";

        }
        else{
            echo "X] $k tidak perlu dicek jumlahnya (".$time."-".$item['time']." = ". ($time - $item['time']) .")\n";
        }
    }
    echo "\n--channels_check---\n";
    print_r($channels_check);
    $channels_berubah = [];
    foreach($channels_check as $item){
        $cnt = f("bot_kirim_perintah")("getChatMemberCount",[
            'chat_id'=>"@$item",
        ]);
        if(empty($cnt['ok'])){
            echo "ERROR! jumlah subscriber $item tidak bisa didapatkan.\n";
            continue;
        }
        $cnt = $cnt['result'];
        if($channels[$item]['cnt'] != $cnt){
            $channels_berubah[]=$item;
            echo "$item berubah dari ".$channels[$item]['cnt']." menjadi $cnt\n";
        }
        else{
            echo "$item tidak berubah ($cnt)\n";
        }
        f("data_save")("channelsbcount/$chname",$cnt);
    }
    
    echo "\n---channels_berubah--\n";
    print_r($channels_berubah);
    $all_users = f("data_list")("users");
    echo "\n---all_users--\n";
    print_r($all_users);
    $usertime = [];
    foreach($all_users as $item){
        $usertime[$item] = f("data_time")("usersubs/$item");
    }
    arsort($usertime);
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
        if($time - $usr_t > $detik_check_subs_all_users){
            $users_check[$usr] = $usersubs;
            echo "V] $usr perlu dicek (".$time."-".$usr_t." = ". ($time - $usr_t) ." > $detik_check_subs_all_users)\n";
        }
        else{
            echo "X] $usr tidak perlu dicek (".$time."-".$usr_t." = ". ($time - $usr_t) .")\n";
        }
        f("set_usersubs")($usr,$usersubs);
        $loopcount++;
    }
    echo "\n---users_check--\n";
    print_r($users_check);
    
    // $delay = $current_time - $last_job_run;
    
}
else{
    echo "job $current_time has been already executed";
}