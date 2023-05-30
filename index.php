<?php
echo "<h1>FileShareBot</h1>";
if(file_exists('config.php')){
    echo "<p><a href='setwebhook.php'>setwebhook</a></p>";
}
else{
    echo "CONFIG FILE DOES NOT EXIST!";
}
