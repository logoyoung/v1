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
    $res = $db->field('uid')->where("ctime>='$stime' and ctime<='$etime' and stime!='0000-00-00 00:00:00' group by uid")->select("live");
    if(false !== $res){
        return $res;
    }else{
        return array();
    }
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
    $sql = "insert into live_length (`uid`, `date`,`length`,`utime`) value($uid,'$date',$length,'$utime') on duplicate key update utime='$utime',length =$length";
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
		return array();
	}else{
		return true;
	}

}
function doEveryThing($date, $db)
{
    $uidsList = getOneDayUids($date,$db);//获取uids
    foreach ($uidsList as $v){
		$liveList=getOneDayLive($date,$v['uid'],$db);
		$times = array();
		foreach ($liveList as $v1){
			$ltime = strtotime($v1['etime']) - strtotime($v1['stime']);
			$isOver = checkIsOverTime($v1['liveid'], $db);//校验是否超时
			if ($isOver) {
				$diff = $ltime - 600;
				if ($diff < 0) {
					$ltime = 0;
				}
			}
			if ($ltime >= 180) {
				array_push($times, $ltime);
			}
		}
		$length = array_sum($times);
		add_Length($v1['uid'], $date, $length, $db);
    }
}

$date = date("Y-m-d", strtotime("-1 day"));
doEveryThing($date, $db);

