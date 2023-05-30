<?php
$GLOBALS['global_db_connect'] = new mysqli("localhost",f("get_config")("db_user"),f("get_config")("db_password"),f("get_config")("db_database"));
if ($GLOBALS['global_db_connect'] -> connect_errno) {
    echo "Failed to connect to MySQL: " . $GLOBALS['global_db_connect'] -> connect_error;
    exit();
}
function db_connect(){
    return $GLOBALS['global_db_connect'];
}