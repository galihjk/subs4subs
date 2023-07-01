<?php
include("init.php");
$last_job_run = f("data_load")("last_job_run",0);
$current_time = time();
echo "<h1>Running job [$current_time]</h1>";
if($last_job_run != $current_time){
    f("data_save")("last_job_run",$current_time);

    echo "<pre>";
    echo "current_time=$current_time - last_job_run=$last_job_run ==== ".($current_time-$last_job_run)."\n";
    
    echo f("check_unsubscribe_user")();
    
}
else{
    echo "job $current_time has been already executed";
}