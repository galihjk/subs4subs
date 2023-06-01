<?php
function get_userchannelmsgid($userid,$channel){
    return f("data_load")("channelposts/$userid-$channel",false);
}