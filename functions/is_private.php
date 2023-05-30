<?php
function is_private($botdata){
    if(f("str_is_diawali")($botdata["chat"]["id"],"-")) return false;
    return true;
}