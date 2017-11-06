<?php

include '../../../include/init.php';
require(INCLUDE_DIR . 'User.class.php');
/**
 * 主播人气
 * date 2016-12-05 19:47
 * author yandong@6rooms.com
 */

$db = new DBHelperi_huanpeng();


//$r=json_encode(array('title'=>'我的人气','list'=>'20,30,40,50,60,70,20,30,40'));
//echo  $_GET["jsoncallback"] . $r;exit;

function getPopularity($uid, $smonth, $emonth, $db)
{
    if (empty($uid)) {
        return false;
    }
    $res = $db->field("ctime,max(popular)  as popular")->where("uid=$uid  and ctime>= '$smonth' and ctime <='$emonth' group by day(ctime)")->order('id ASC')->select('anchor_most_popular');
    if (false !== $res) {
        if (empty($res)) {
            return array();
        } else {
            foreach ($res as $v) {
                $d = substr($v['ctime'], 0, 10);
                $temp[$d] = $v['popular'];
            }
            return $temp;
        }
    } else {
        return false;
    }
}

function getMouthDays($date)
{
    $month = date("Y-m", strtotime($date));
    $days = date('t', strtotime($date));
    return array($month, $days);
}


function   getMonthLiveLength($uid,$month,$db){
    if(empty($uid) ||empty($month)){
        return false;
    }
    $smonth=$month.'-01';
    $emonth=$month.'-31';
    $res=$db->field("sum(length) as total")->where("uid=$uid  and  date >= '$smonth'  and  date <='$emonth'")->select('live_length');
    if(false !== $res){
        return $res[0]['total']?$res[0]['total']:0;
    }else{
        return 0;
    }
}


function getMonth($month){
    if ($month) {
        $thisMonth = (int)(date('m'));
        if ($thisMonth < $month) {//说明是上一年
            $lastyear = ((int)date('Y') - 1);//上一年
            $monthDetail = getMouthDays($lastyear . "-" . $month);

        } else {
            $monthDetail = getMouthDays(date('Y', time()) . '-' . $month);

        }
    } else {
        $monthDetail = getMouthDays(date('Y', time()) - date('m', time()));
    }
    return $monthDetail;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '170';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '9db06bcff9248837f86d1a6bcf41c9e7';
$month = isset($_POST['month']) ? (int)($_POST['month']) : '';
if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
//检查用户登陆状态
$userState = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $userState) {
    error2(-4067,2);
}

if (!in_array($month, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12))) {
    error2(-4017, 2);
}

$monthDay=getMonth($month);
$smonth = $monthDay[0] . '-' . '01 00:00:00';
$emonth = $monthDay[0] . '-' . $monthDay[1] . '23:59:59';

$timelength=getMonthLiveLength($uid,$monthDay[0] ,$db);
$d = floor($timelength / 3600 / 24);
$h = floor(($timelength % (3600 * 24)) / 3600);  //%取余
$m = floor(($timelength % (3600 * 24)) % 3600 / 60);
$llength = (24 * $d) + $h . '小时' . $m . '分钟';

$res = getPopularity($uid, $smonth, $emonth, $db);
if (false !== $res) {
    $list = array();
    if ($res) {
        for ($m = 1; $m < $monthDay[1] + 1; $m++) {
            if ($m < 10) {
                $m = "0" . $m;
            }
            $day[] = $monthDay[0] . '-' . $m;
        }
        foreach($day as $v){
            if (array_key_exists($v, $res)) {
                $temp[] = $res[$v];
            } else {
                $temp[] = 0;
            }

        }
    } else {
        for ($i = 0; $i <$monthDay[1]; $i++) {
            $temp[] = 0;
        }
    }
    succ(array('title' => '我的人气', 'list' => $temp,'monthLiveTime'=>$llength));
} else {
    error2(-5017);
}