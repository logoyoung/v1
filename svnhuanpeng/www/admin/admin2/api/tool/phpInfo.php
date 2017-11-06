<?php
header("Content-Type: text/html;charset=utf-8");
//include '../../../../include/init.php';
//$db = new DBHelperi_huanpeng();
include '../../includeAdmin/init.php';
include '../../includeAdmin/Redis.class.php';
$db = new DBHelperi_admin();

function get_uids($db)
{
    $sql = " select uid from   live  where  etime !='0000-00-00 00:00:00'  and ctime > '2017-02-08 00:00:00'  and  ctime < '2017-02-28 23:59:59'  group  by  uid;";
    $res = $db->doSql($sql);
    if ($res) {
        return array_column($res, 'uid');
    } else {
        return false;
    }

}

function addLength($uid, $date, $length, $db)
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


function listLive($uid, $db)
{

    $res = $db->field('uid,liveid,ctime,stime,etime')->where("uid=$uid and ctime > '2017-02-08 00:00:00'  and  ctime < '2017-02-28 00:00:00'")->select('live');
    if (false !== $res) {
        if (!empty($res)) {
            $list = array();
            $status = false;
            foreach ($res as $v) {
                $sub = substr($v['ctime'], 0, 10);

                $retime = 0;
                if ($v['stime'] == '0000-00-00 00:00:00') {
                    if ($v['etime'] != '0000-00-00 00:00:00') {
                        $status = true;
                        $ltime = strtotime($v['etime']) - strtotime($v['ctime']);
                    }
                } else {
                    if ($v['etime'] != '0000-00-00 00:00:00') {
                        $status = true;
                        $ltime = strtotime($v['etime']) - strtotime($v['stime']);
                    }

                }

                if ($status) {
                    $over = $db->where("liveid=" . $v['liveid'] . " and stype=1 ")->limit(1)->select('videosave_queue');
                    if ($over) {
                        $diff = $ltime - 600;
                        if ($diff < 0) {
                            $ltime = 0;
                        }
                    }
                    if ($ltime >= 60) {
                        $retime += $ltime;
                    }
                    if ($retime >= 36400) {
                         $toolong=array();
                         array_push($toolong,$sub);
                    } else {
                        if (array_key_exists($sub, $list)) {
                            $before = $list[$sub];
                            $list[$sub] = ($retime + $before);
                        } else {
                            $list[$sub] = $retime;
                        }
                    }
                }
            }
            if($list){
                if($toolong){
                    for($i=0,$k=count($toolong);$i<$k;$i++){
                        unset($list[$toolong[$i]]);
                    }
                }
                foreach ($list as $k=>$v){
                    addLength($uid, $k,$v, $db);
                }
            }

//            if ($retime) {
//
//                $res=$db->where("date='2017-02-22' and  uid=$uid")->update('live_length', array('length' => $retime));
//                return $res;
//            }
        }
    } else {
        file_put_contents('./number.txt', $uid, FILE_APPEND);
    }
}
//
//$array=get_uids($db);
//for($i=0,$k=count($array);$i<$k;$i++){
//    $re = listLive($array[$i], $db);
//    echo $array[$i].'::::'.PHP_EOL;
//}

//$redisObj = new RedisHelp();
//$sql='select uid,rtime,phone  from  userstatic   where  rtime in ( select ctime from  task  where uid >4515 and taskid=36)';
//$res=$db->doSql($sql);
//foreach($res as $v){
//    $ctime=$v['rtime'];
//    $r=$db->where("ctime ='$ctime'")->update('task',array('uid'=>$v['uid']));
//    echo $r.PHP_EOL;
//}


//$phone=array_column($res,'phone');
//for ($i=0,$k=count($phone);$i<$k;$i++){
//    $res=$db->field('uid')->where("phone=".$phone[$i])->limit(1)->select('userstatic');
//    if($res){
//echo 44;
//        $keys = "IsFirstLoginfromApp:" . 3540;
//        $r = $redisObj->get($keys);
//        echo $r.PHP_EOL;
//    }
//}
