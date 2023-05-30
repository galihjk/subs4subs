<?php
function handle_botdata_functions($botdata, $functions){
    $result = false;
    foreach($functions as $function){
        $result = f($function)($botdata);
        if($result) return true;
    }
    return false;
}