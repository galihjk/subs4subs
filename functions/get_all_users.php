<?php
function get_all_users(){
    $users = [];
    $folder = "data/users";
    if (is_dir($folder)){
        foreach(scandir($folder) as $file){
            if(!in_array($file,['..', '.'])){
                $users[] = substr($file, 0,-5);
            }
        }
    }
    return $users;
}
