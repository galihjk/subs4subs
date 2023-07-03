<?php
include("init.php");
$last_job_run = f("data_load")("last_job_run",0);
$current_time = time();
echo "<h1>Running job [$current_time]</h1>";
if($last_job_run != $current_time){
    f("data_save")("last_job_run",$current_time);

    echo "<pre>";
    echo "current_time=$current_time - last_job_run=$last_job_run ==== ".($current_time-$last_job_run)."\n";
    
    $hasil_job = f("check_unsubscribe_user")();
    echo $hasil_job;
    file_put_contents("log/LAST_JOB_".date("Y-m").".txt",$hasil_job);
    
}
else{
    echo "job $current_time has been already executed";
}