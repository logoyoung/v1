<?php
/**
 * Created by PhpStorm. 直播时长统计
 * User: dong
 * Date: 17/3/12
 * Time: 下午9:45
 */
require '/usr/local/huanpeng/include/init.php';
//$GLOBALS['env']='PRO';
$db = new DBHelperi_huanpeng();

function getOneDayUids($date, $db)
{
    $stime = $date . ' 00:00:00';
    $etime = $date . ' 23:59:59';
	$sql = "select uid from live where stime!='0000-00-00 00:00:00' and (ctime>='$stime' or etime>='$stime')";
    
	$res = $db->doSql($sql);
	
	return $res;
}

/**获取某一天的直播时长
 * @param $date
 * @param $db
 * @return array
 */
function getOneDayLive($date, $uid, $db)
{
    $stime = $date . ' 00:00:00';
    $etime = $date . ' 23:59:59';
    $res = $db->field('stime,etime,uid,liveid')
			->where("uid=$uid and status>" . LIVE . " and ctime>='$stime' and ctime<='$etime' and etime!='0000-00-00 00:00:00' and stime != '0000-00-00 00:00:00'")
			->select("live");
    if(false !== $res){
        return $res;
    }else{
        return array();
    }

}

function add_Length($uid, $date, $length, $db)
{
    if (!$uid || !$length) {
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


function checkIsOverTime($liveid, $db)
{
    $over = $db->where("liveid=" . $liveid . " and stype=1 ")->limit(1)->select('videosave_queue');

	if(empty($over)){
		return false;
	}else{
		return true;
	}

}
function doEveryThing($date, $db)
{
    $uidsList = getOneDayUids($date,$db);//获取uids
    foreach ($uidsList as $v){
		$liveList=getOneDayLive($date,$v['uid'],$db);
		$length = 0;
		foreach ($liveList as $v1){
			$time1 = strtotime($date);
			$time2 = $time1 + 24*3600;
			$stime = strtotime($v1['stime']);
			$etime = strtotime($v1['etime']);
			if($stime < $time1) { //开始时间早于当天的，从00:00开始算
				$stime = $time1;
			}
			if($etime > $time2) { //结束时间晚于当天的，时间截止到24:00
				$etime = $time2;
			}
			$ltime = $etime - $stime;
			$isOver = checkIsOverTime($v1['liveid'], $db);//校验是否超时
			if ($isOver) {
				$diff = $ltime - 600; //超时要减掉10分钟
				if ($diff < 0) {
					$ltime = 0;
				}
			}
			if ($ltime >= 180) { //时长小于三分钟的不算
				$length += $ltime;
			}
		}
		add_Length($v1['uid'], $date, $length, $db);
    }
}

$date = date("Y-m-d", strtotime("-1 day"));
doEveryThing($date, $db);

