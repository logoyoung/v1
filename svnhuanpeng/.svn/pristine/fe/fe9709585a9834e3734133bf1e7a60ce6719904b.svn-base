<?php
/**
 * 今日直播时长统计    每10分钟统计一次
 * 需要重新统计昨天的数据
 * User: dong
 * Date: 17/3/12
 * Time: 下午9:45
 */
require '/usr/local/huanpeng/include/init.php';
//$GLOBALS['env']='PRO';
$db = new DBHelperi_huanpeng(true);


if(time() - strtotime(date('Y-m-d')) < 1000) { //统计昨天数据
	$stime = date('Y-m-d', strtotime('-1 day'));
	$etime = date('Y-m-d') . ' 00:00:00';
	doSomething( $stime, $etime, $db );
}
$stime = date('Y-m-d');
$etime = date('Y-m-d H:i:s');
doSomething( $stime, $etime, $db );


function add_Length($uid, $date, $length, $db)
{
    if (!$uid || !$date || !$length) {
        return false;
    }
	$utime = date('Y-m-d H:i:s');
	$sql = "insert into live_length_copy (`uid`, `date`,`length`,`utime`) value($uid,'$date',$length,'$utime') on duplicate key update utime='$utime',length =$length";
	$res = $db->query($sql);
	
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

/**
 * 根据数据库返回的结果计算每个主播的直播时长
 */
function getLength($res, $stime)
{
	$uids = array_unique(array_column($res, 'uid'));
	$arr = array();
	foreach($uids as $k=>$v) {
		$length = 0;
		foreach($res as $kk=>$vv) {
			if($vv['uid'] == $v) {
				if($vv['stime'] < $stime) {  //如果开始时间早于统计的这一天
					$start = strtotime($stime);
				} else {
					$start = strtotime($vv['stime']);  //流开始时间
				}
				if($vv['etime'] == '0000-00-00 00:00:00') {
					if(date('Y-m-d') == $stime) {  //如果没有结束时间且统计的是今天是数据
						$end = time();
					} else {
						$end = strtotime(date('Y-m-d')); //如果不是当天，结束时间为24点
					}
				} else {
					$end = strtotime($vv['etime']);  //流结束时间
				}
				$num = $end - $start;
				if($num < 0) {
					echo $v . "cuowu" . chr(10);
					continue;//错误日志
				}
				if($v == 2655) {
					echo $num . 'test' . chr(10);
				}
				$length += $num;
			}
		}
		$arr[$v] = $length;
	}
	return $arr;
}

function doSomething($stime, $etime, $db )
{
	
	$sstime = date('Y-m-d', strtotime($stime) - 3600 * 24); //数据库里有没有结束时间的脏数据，这里过滤一下
	
	$sql = "select liveStreamRecord.*, live.uid as uid from liveStreamRecord join live on liveStreamRecord.liveid=live.liveid where 
			liveStreamRecord.stime<'$etime' and liveStreamRecord.stime>'$sstime' and liveStreamRecord.stime!='0000-00-00 00:00:00' 
			and (liveStreamRecord.etime='0000-00-00 00:00:00' or liveStreamRecord.etime>'$stime')";
	$res = $db->doSql($sql);		
    //$res = $db->field('*')->where("stime<'$etime' and stime!='0000-00-00 00:00:00' and (etime='0000-00-00 00:00:00' or etime>'$stime')")->select("liveStreamRecord");

	$length = getLength($res, $stime);
	foreach($length as $k=>$v) {
		add_Length($k, $stime, $v, $db); 
	}
}


