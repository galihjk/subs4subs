<?php
function db_select_one($q){
    $q .= " limit 1";
    $data = [];
    $db = f("db_q")($q);
    if(!empty($db[0])) $data = $db[0];
    return $data;
}