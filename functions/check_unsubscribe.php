<?php
function check_unsubscribe($botdata){
    $user = $botdata['from'];
    $userid = $user['id'];
    $usersubs = f("get_usersubs")($userid);
    $usersbp = f("get_usersbp")($userid);
    $newusersubs = $usersubs;
    $unsubscribe = false;
    foreach($usersubs as $k=>$channel){
        $getChatMember = f("bot_kirim_perintah")("getChatMember",[
            'chat_id'=>$channel,
            'user_id'=>$userid,
        ]);
        if(empty($getChatMember["result"]["status"])
        or in_array($getChatMember["result"]["status"],["restricted","left","kicked"])
        ){
            f("bot_kirim_perintah")("sendMessage",[
                'chat_id'=>$userid,
                'text'=>"Anda telah unsubscribe $channel, SBP -1",
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
    return false;
}