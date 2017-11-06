<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/7/1
 * Time: 下午4:57
 */
include_once '../../includeAdmin/init.php';
include_once INCLUDE_DIR . 'statistics/Userstatistic.class.php';

$db = new DBHelperi_admin();
$range = (int)$_GET['range'] ? (int)$_GET['range'] : 30;

$statistic = new UserStatistic($db);

$ret = array();
for($i=$range; $i > 0; $i --){
    $date = date("Y-m-d" ,strtotime("- $i days"));
    $ret[]['date'] = $date;
    $ret[]['value'] = $statistic->newUsers($date.$statistic::d_start, $date.$statistic::d_end);
}

exit(json_encode($ret));


