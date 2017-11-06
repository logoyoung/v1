<?php
/**
 * Created by PhpStorm. 直播时长统计
 * User: dong
 * Date: 17/3/12
 * Time: 下午9:45
 */
require '/usr/local/huanpeng/include/init.php';
$GLOBALS['env']='PRO';
$db = new DBHelperi_huanpeng();
function getOneDayUids($date,$db){
    $stiem=$date.' 00:00:00';
    $etime=$date. ' 23:59:59';
    $res=$db->field('uid')->where("ctime >= '$stiem' and  ctime <= '$etime' and stime != '0000-00-00 00:00:00' group by uid")->select("live");
    if(false !==$res){
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
function  getOneDayLive($date,$uid,$db){
    $stiem=$date.' 00:00:00';
    $etime=$date. ' 23:59:59';
    $res=$db->field('stime,etime,uid,liveid')->where("uid= $uid  and  status > ".LIVE."  and ctime >= '$stiem' and  ctime <= '$etime' and  etime != '0000-00-00 00:00:00' and  stime != '0000-00-00 00:00:00'")->select("live");
    if(false !==$res){
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
    $utime = date('Y-m-d H:i:s', time());
    $sql = "insert into live_length (`uid`, `date`,`length`,`utime`) value($uid,'$date',$length,'$utime') on duplicate key update utime='$utime',length =$length";
    $res = $db->query($sql);
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}


function chaeckIsOverTime($liveid,$db){
    $over = $db->where("liveid=" . $liveid . " and stype=1 ")->limit(1)->select('videosave_queue');
    if(false !==$over){
        if(empty($over)){
            return array();
        }else{
            return true;
        }
    }else{
        return false;
    }

}
function  doEveryThing($date,$db){
//    $uidsList=getOneDayUids($date,$db);//获取uids
//    foreach ($uidsList  as $v){
        $liveList=getOneDayLive($date,3415,$db);
        $times=array();
        foreach ($liveList as $v1){
            $ltime=strtotime($v1['etime']) - strtotime($v1['stime']);
            $isOver=chaeckIsOverTime($v1['liveid'],$db);//校验是否超时
            if ($isOver) {
                $diff = $ltime - 600;
                if ($diff < 0) {
                    $ltime = 0;
                }
            }
            if ($ltime >= 180) {
                array_push($times,$ltime);
            }
        }
        $length=array_sum($times);
        return $length;
//     $res= add_Length($v1['uid'], $date, $length, $db);
//    }
}


//
//$l=array();
//for ($i=1;$i<16;$i++){
//    if($i<10){
//        $date='2017-03-0'.$i;
//    }else{
//        $date='2017-03-'.$i;
//    }
//  $length=doEveryThing($date,$db);
//    array_push($l,$length);
//
//}
////$date='2017-03-13';
//////$l= doEveryThing($date,$db);
//var_dump($l);
//echo "<hr/>";
//var_dump(array_sum($l));



function  gLive($uid,$db){
    $res=$db->field('stime,etime,uid,liveid')->where("uid= $uid  and  status > ".LIVE."  and  etime != '0000-00-00 00:00:00' and  stime != '0000-00-00 00:00:00'")->select("live");
    if(false !==$res){
        $l=array();
        foreach ($res as $v){
            $etime=strtotime($v['etime']);
            $stime=strtotime($v['stime']);
            $len=$etime - $stime;
            array_push($l,$len);
        }
        return $l;
    }else{
        return array();
    }

}

$res=gLive(3415,$db);
var_dump($res);
echo "<hr/>";
var_dump(array_sum($res));












//function getOneDayUids($date,$db){
//    $stiem=$date.' 00:00:00';
//    $etime=$date. ' 23:59:59';
//    $res=$db->field('uid')->where("ctime >= '$stiem' and  ctime <= '$etime' and stime != '0000-00-00 00:00:00' group by uid")->select("live");
//    if(false !==$res){
//        return $res;
//    }else{
//        return array();
//    }
//}

/**获取某一天的直播时长
 * @param $date
 * @param $db
 * @return array
 */
//function  getOneDayLive($date,$uid,$db){
//    $stiem=$date.' 00:00:00';
//    $etime=$date. ' 23:59:59';
//    $res=$db->field('stime,etime,uid,liveid')->where("uid= $uid  and  status > ".LIVE."  and ctime >= '$stiem' and  ctime <= '$etime' and etime != '0000-00-00 00:00:00' and stime != '0000-00-00 00:00:00'")->select("live");
//    if(false !==$res){
//        return $res;
//    }else{
//        return array();
//    }
//
//}
//
//function add_Length($uid, $date, $length, $db)
//{
//    if (!$uid || !$length) {
//        return false;
//    }
//    $utime = date('Y-m-d H:i:s', time());
//    $sql = "insert into live_length (`uid`, `date`,`length`,`utime`) value($uid,'$date',$length,'$utime') on duplicate key update utime='$utime',length =$length";
//    $res = $db->query($sql);
//    if (false !== $res) {
//        return true;
//    } else {
//        return false;
//    }
//}


//function chaeckIsOverTime($liveid,$db){
//    $over = $db->where("liveid=" . $liveid . " and stype=1 ")->limit(1)->select('videosave_queue');
//    if(false !==$over){
//        if(empty($over)){
//            return array();
//        }else{
//            return true;
//        }
//    }else{
//        return false;
//    }
//
////}
//function  doEveryThing($date,$db){
//    $uidsList=getOneDayUids($date,$db);//获取uids
//    foreach ($uidsList  as $v){
//        $liveList=getOneDayLive($date,$v['uid'],$db);
//        $times=array();
//        $list=array();
//        foreach ($liveList as $v1){
//            $ltime=strtotime($v1['etime']) - strtotime($v1['stime']);
//            $isOver=chaeckIsOverTime($v1['liveid'],$db);//校验是否超时
//            if ($isOver) {
//                $diff = $ltime - 600;
//                if ($diff < 0) {
//                    $ltime = 0;
//                }
//            }
//            if ($ltime >= 180) {
//                array_push($times,$ltime);
//            }
//        }
//        $length=array_sum($times);
//       add_Length($v1['uid'], $date, $length, $db);
//    }
////return $length;
//}

//$date=date("Y-m-d",strtotime("-1 day"));
//$l=array();
//for ($i=14;$i<15;$i++){
//    if($i<10){
//        $date='2017-03-0'.$i;
//    }else{
//        $date='2017-03-'.$i;
//    }
//  doEveryThing($date,$db);
//
//}
////$date='2017-03-13';
//////$l= doEveryThing($date,$db);
//var_dump($l);
//echo "<hr/>";
//var_dump(array_sum($l));


//function updateStime($db){
//    $isStime=$db->field('liveid,ctime')->select('live');
//    foreach ($isStime  as  $v){
//        $db->where("liveid=".$v["liveid"])->update("live",array('stime'=>$v["ctime"]));
//    }
//}
//
//updateStime($db);










































//function getOneDayUids($date,$db){
//    $stiem=$date.' 00:00:00';
//    $etime=$date. ' 23:59:59';
//    $res=$db->field('uid')->where("ctime >= '$stiem' and  ctime <= '$etime' and stime != '0000-00-00 00:00:00' group by uid")->select("live");
//    if(false !==$res){
//        return $res;
//    }else{
//        return array();
//    }
//}
//
///**获取某一天的直播时长
// * @param $date
// * @param $db
// * @return array
// */
//function  getOneDayLive($date,$uid,$db){
//    $stiem=$date.' 00:00:00';
//    $etime=$date. ' 23:59:59';
//    $res=$db->field('stime,etime,uid,liveid')->where("uid= $uid  and  status > 100 and ctime >= '$stiem' and  ctime <= '$etime' and stime != '0000-00-00 00:00:00'")->select("live");
//    if(false !==$res){
//         return $res;
//    }else{
//        return array();
//    }
//
//}
//
//function add_Length($uid, $date, $length, $db)
//{
//    if (!$uid || !$length) {
//        return false;
//    }
//    $utime = date('Y-m-d H:i:s', time());
//    $sql = "insert into live_length (`uid`, `date`,`length`,`utime`) value($uid,'$date',$length,'$utime') on duplicate key update utime='$utime',length =$length";
//    $res = $db->query($sql);
//    if (false !== $res) {
//        return true;
//    } else {
//        return false;
//    }
//}
//
//
//function chaeckIsOverTime($liveid,$db){
//    $over = $db->where("liveid=" . $liveid . " and stype=1 ")->limit(1)->select('videosave_queue');
//    if(false !==$over){
//        if(empty($over)){
//            return array();
//        }else{
//            return true;
//        }
//    }else{
//       return false;
//    }
//
//}
//function  doEveryThing($date,$db){
////    $uidsList=getOneDayUids($date,$db);//获取uids
////    foreach ($uidsList  as $v){
//        $liveList=getOneDayLive($date,4380,$db);
//        $times=array();
//        foreach ($liveList as $v1){
//            $ltime=strtotime($v1['etime']) - strtotime($v1['stime']);
//            $isOver=chaeckIsOverTime($v1['liveid'],$db);//校验是否超时
//            if ($isOver) {
//                $diff = $ltime - 600;
//                if ($diff < 0) {
//                    $ltime = 0;
//                }
//            }
//            if ($ltime >= 180) {
//                array_push($times,$ltime);
//            }
//        }
//        $length=array_sum($times);
//    add_Length($v1['uid'], $date, $length, $db);
////    }
//    return $length;
//}
//
//$la=array();
//for ($i=1;$i<13;$i++){
//    if($i<10){
//        $date='2017-03-0'.$i;
//    }else{
//        $date='2017-03-'.$i;
//    }
//   $length= doEveryThing($date,$db);
//    array_push($la,$length);
//}
//var_dump(array_sum($la));
