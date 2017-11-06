<?php

/**
 * 获取当前直播列表
 * date 2017-02-24 09:45
 * author yandong@6rooms.com
 */
include '../../../include/init.php';
$db = new DBHelperi_huanpeng();
$redisObj = new RedisHelp();

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

function getPopularoty($date, $db)
{
    $stime = $date . ' 00:00:00';
    $etime = $date . ' 23:59:59';
    $res = $db->where(" date >='$stime' and date <= '$etime'  group  by uid")->select('popularoty_record');
    if (false !== $res) {
        if (empty($res)) {
            return array();
        } else {
            return array_column($res, 'uid');
        }
    } else {
        return array();
    }
}

function checkLiveLength($date, $uids, $db)
{
    $uid = implode(',', $uids);
    $res = $db->field('uid,length')->where("date ='$date' and uid in ($uid)  and  length >3600  group  by uid ")->select('live_length');
    if (false !== $res) {
        if (empty($res)) {
            return array();
        } else {
            return array_column($res, 'uid');
        }
    } else {
        return array();
    }

}

function popularoty($date, $uids, $db)
{
    $maxList = array();
    for ($i = 0, $k = count($uids); $i < $k; $i++) {
        $list = array();
        for ($m = 0; $m < 21; $m++) {
            $j = ($m + 4);
            if ($m < 10) {
                $stime = $date . " 0$m:00:00";
                if ($j < 10) {
                    $etime = $date . " 0$j:00:00";
                } else {
                    $etime = $date . " $j:00:00";
                }
            } else {
                $stime = $date . " $m:00:00";
                $etime = $date . " $j:00:00";
            }
            $res = $db->field("uid,sum(popularoty) as total")->where("uid=" . $uids[$i] . " and date BETWEEN ' $stime ' and '$etime'")->select('popularoty_record');
            if (false !== $res) {
                if (!empty($res[0]['total'])) {
                    $total = $res[0]['total'];
                } else {
                    $total = 0;
                }
                array_push($list, $total);
            }
        }
        $max = max($list);
        array_push($maxList, array('uid' => $uids[$i], 'popular' => $max));
    }
    return $maxList;
}

function gePopuList($type, $page, $size, $db, $redisObj, $conf)
{
    $plist = array();
    if ($type == 1) {
        $date = '2017-03-08';
    }
    if ($type == 2) {
        $date = '2017-03-09';
    }
    if ($type == 3) {
        $date = '2017-03-10';
    }
    $Addrkey = "POPULAROTY_RECORD" . $date;
    $getCatch = $redisObj->get($Addrkey);
    if ($getCatch) {
        $afteroder = json_decode($getCatch, true);
    } else {
        $uids = getPopularoty($date, $db);
        if (empty($uids)) {
            return array('list' => array(), 'total' => 0);
        } else {
            $ok = checkLiveLength($date, $uids, $db);
            if (empty($ok)) {
                return array('list' => array(), 'total' => 0);
            } else {
                $res = popularoty($date, $ok, $db);
                if ($res) {
                    $uidlist = array_column($res, 'uid');
                    $userInfo = getUserInfo($uidlist, $db);
                    foreach ($res as $k => $v) {
                        $temp['uid'] = $v['uid'];
                        $temp['popular'] = $v['popular'];
                        $temp['nick'] = array_key_exists($v['uid'], $userInfo) ? $userInfo[$v['uid']]['nick'] : '欢朋';
                        $temp['url'] = "http://www.huanpeng.com/sharer.php?luid=" . $v['uid'] . "&datamain=6cn";
                        $temp['head'] = !empty($userInfo[$v['uid']]['pic']) ? DOMAIN_PROTOCOL . $conf['domain-img'] . "/" . $userInfo[$v['uid']]['pic'] : DEFAULT_PIC;
                        array_push($plist, $temp);
                    }
                    $afteroder = dyadicArray($plist, 'popular', SORT_DESC);
                    $redisObj->set($Addrkey, json_encode($afteroder), 3600);
                } else {
                    $afteroder = array();
                }
            }

        }
    }
    $page = returnPage(count($afteroder), $size, $page);
    $offect = ($page - 1) * $size;
    $finallyLists = array_slice($afteroder, $offect, $size);
    return array('list' => $finallyLists, 'total' => count($afteroder));
}

$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$size = isset($_POST['size']) ? (int)$_POST['size'] : 10;
if (!in_array($type, array(1, 2, 3))) {
    error2(-4017);
}

$res = gePopuList($type, $page, $size, $db, $redisObj, $conf);
succ(array('list' => $res['list'], 'total' => $res['total']));