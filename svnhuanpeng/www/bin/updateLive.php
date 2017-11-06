<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/9/12
 * Time: 下午2:29
 */

require(__DIR__.'/../include/init.php');
define('SCAN_LIVE_TIME', 120);

$db = new DBHelperi_huanpeng(true);

while(true){
    $sTime = date("Y-m-d H:i:s", time() - SCAN_LIVE_TIME);
    $eTime = date("Y-m-d H:i:s", time());


    $debug = true;

    $sql = "select liveid from live where status=".LIVE ." and ctime between '$sTime' and '$eTime'";
    $res = $db->query($sql);

    $liveList = array();
    while($row = $res->fetch_assoc()){
        array_push($liveList, $row['liveid']);
    }

    $sql = "select liveid from admin_liveReview where livestatus = 1 and ctime BETWEEN '$sTime' and '$eTime'";
    $res = $db->query($sql);

    $adminList = array();
    while($row = $res->fetch_assoc()) {
        if($row['liveid'])
            array_push($adminList, $row['liveid']);
    }

    foreach($liveList as $value){
        if(in_array($value, $adminList)){
            $r = liveStatusMsgToAdmin($value, 1);
        }

    }

    sleep(60);
}




