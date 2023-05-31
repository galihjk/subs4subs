<?php
function s4slink($msgid = ""){
    return str_replace("@", "t.me/",f("get_config")("s4s_channel")) . "/$msgid";
}