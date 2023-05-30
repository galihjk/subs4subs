<?php
function f($f){
    $filename = "/functions/$f.php";
    if(file_exists($filename)){
        include_once($filename);
        return $f;
    }
    file_put_contents("last_error.txt", "f_not_exist: $f!\n");
    return false;
}