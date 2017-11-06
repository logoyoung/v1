<?php
/**
 * 报表
 * 3月1日-5月9日开播统计
 * 每日开播的直播路数、每日同时直播的路数峰值
 */

set_time_limit(300);
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);
include '../includeAdmin/init.php';

$db = new DBHelperi_admin();


$date = '2017-05-01';
$days = 11;
$time = strtotime($date);

$str = '<html><body><table>';
for($i = 0; $i < $days; $i++) 
{
	$today = $time + 3600 * 24 * $i;
	$start = date('Y-m-d H:i:s', $today);
	$end = date('Y-m-d H:i:s', $today + 3600 * 24);
	
	$sql = "select ctime,etime from live where (ctime>'$start' and ctime<'$end') or (ctime<'$start' and etime>'$start')";
	
	$res = $db->doSql($sql);
	$count = count($res);
	
	$top = array();
	foreach($res as $k=>$v) {
		$ctime = strtotime($v['ctime']) - $today;
		$etime = strtotime($v['etime']) - $today;
		if($ctime < 0) {
			$ctime = 0;
		}
		if($etime >= 86400) {
			$etime = 86399;
		}
		
		for($j = $ctime; $j <= $etime; $j++) {
			if(isset($top[$j])) {
				$top[$j] += 1;
			} else {
				$top[$j] = 1;
			}
		}		
	}
	$test = $top;
	rsort($top);
	
	$str .= '<tr><td>' . explode(' ', $start)[0] . '</td><td>' . $count . '</td><td>' . $top[0] . '</td><td>' 
			. date('Y-m-d H:i:s', $today + array_search($top[0], $test)) . '</td></td>';
}

$str .= '</table></body></html>';

echo $str;