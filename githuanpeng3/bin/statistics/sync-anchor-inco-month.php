<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/1/20
 * Time: 下午1:45
 */

include_once '/usr/local/huanpeng/include/init.php';

if(($argv[2])){
	$time = $argv[2] - 1200;
}else{
	$time = time() - 1200;
}

$cur_stime = date('Y-m-d ',$time)."00:00:00";
$cur_etime = date('Y-m-d ',$time)."23:59:59";

echo "==========  doing  month sync start ==========\n\n";
mylog("current date is ".$cur_stime, LOGFN_SENDGIFT_LOG);


$db = new DBHelperi_huanpeng();

$sql = "select sum(coin) as coin,sum(bean) as bean,uid ,liveid, companyid from log_anchor_income_day where `date` between '$cur_stime' and '$cur_etime' GROUP by liveid";

$res = $db->query($sql);

$list = array();

while($row = $res->fetch_assoc()){
	array_push($list, $row);
}

foreach ($list as $dayInfo){
	$dayInfo['utime'] = date('Y-m-d H:i:s');
	$dayInfo['date'] = $cur_stime;

	$update['coin'] = $dayInfo['coin'];
	$update['bean'] = $dayInfo['bean'];
	$update['utime'] = $dayInfo['utime'];

	$sql = $db->insertDuplicate('log_anchor_income_month',$dayInfo,$update, true)." ,count_t=count_t+1";
	$db->query($sql);
	$affectrows = $db->affectedRows;

	mylog( "liveid {$dayInfo['liveid']} is finish  \n the affectrows is $affectrows \n", LOGFN_SENDGIFT_LOG);
	echo "\n";
}