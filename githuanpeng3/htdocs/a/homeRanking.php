<?php

include '../init.php';
require_once(INCLUDE_DIR . 'redis.class.php');
/**
 * 首页排行榜
 * author yandong@6rooms.com
 * date 2016-02-01 10:04
 * copyright@6.cn version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
$redobj = new RedisHelp();

/**
 * 收入排行
 * @param int $userType
 * @param int $timeType
 * @param int $size
 * @param object $db
 * @param object $redobj
 * @return array
 */
function getSalaryRank($userType, $timeType, $size, $db, $redobj) {
//    $cacheKey = "HuanPeng_HomeYesterdayRankingBy$userType$timeType";
    $getCatch = '';
    // $getCatch = $redobj->get($cacheKey);
    if ($getCatch) {
//        $res = jsond($getCatch, true);
    } else {
        if ($timeType == 1) {
            $beginTime = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
            $endTime = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
        }
        if ($timeType == 2) {
            $thisweek = ThisWeekStartEnd();
            $beginTime = $thisweek['start'];
            $endTime = $thisweek['end'];
        }
        if ($timeType == 3) {
            $beginTime = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), 1, date('Y')));
            $endTime = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('t'), date('Y')));
        }

        if ($userType == 1) {
            $res = $db->field('beneficiaryid,sum(income) as coin')->where("ctime >='$beginTime' and ctime<='$endTime' and beneficiaryid != 0  group by beneficiaryid")
                            ->order('coin DESC')->limit($size)->select('billdetail');
        } else {
            $res = $db->field('customerid,sum(purchase) as coin')->where("ctime >= '$beginTime' and ctime <='$endTime' group by customerid")
                            ->order('coin DESC')->limit($size)->select('billdetail');
        }
        //  $redobj->set($cacheKey, jsone($res), $keytime);
    }

    return $res;
}

/**
 * 获取昨天的收入排行
 * @param int $userType
 * @param int $timeType
 * @param int $size
 * @param object $db
 * @param object $redobj
 * @return array
 */
function getYesterdayRank($userType, $timeType, $size, $db, $redobj) {
    $cacheKey = "HuanPeng_HomeYesterdayRankingBy$userType$timeType";
    $getCatch = '';
    // $getCatch = $redobj->get($cacheKey);
    if ($getCatch) {
        $res = jsond($getCatch, true);
    } else {
        $beginYesterday = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
        $endYesterday = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1);
        $atThisTime = time();
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        $keytime = ($endToday) - ($atThisTime);
        if ($userType == 1) {
            $res = $db->field('beneficiaryid,sum(income) as coin')->where("ctime >='$beginYesterday' and ctime<='$endYesterday'  and beneficiaryid != 0  group by beneficiaryid")
                            ->order('coin DESC')->limit($size)->select('billdetail');
        } else {
            $res = $db->field('customerid,sum(purchase) as coin')->where("ctime >= '$beginYesterday' and ctime <='$endYesterday' group by customerid")
                            ->order('coin DESC')->limit($size)->select('billdetail');
        }
        //  $redobj->set($cacheKey, jsone($res), $keytime);
    }

    return $res;
}

/**
 * 获取昨天的人气排行
 * @param int $size
 * @param object $db
 * @param object $redobj
 * @return array
 */
function getYesterdayPopularityRank($size, $db, $redobj) {
    $yesterdayPo = array();
    $beginYesterday = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
    $endYesterday = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1);
    $res = $db->field('luid,sum(giftnum) as huancoin')->where("ctime >='$beginYesterday'  and ctime<='$endYesterday' group by luid")
                    ->order('huancoin DESC')->limit($size)->select('giftrecord');
    if ($res) {
        foreach ($res as $v) {
            $Polist[$v['luid']] = $v;
        }
        $yesterdayPo = array_keys($Polist);
    }
    return $yesterdayPo;
}

/**
 * 批量获取用户昵称
 * date  2016-02-01
 * @param array $uids
 * @param object $db
 * @return array
 */
function getUserPicAndNicks($uids, $db) {
    $s = implode(',', $uids);
    $ret = array();
    $res = $db->field('uid,nick,pic')->where('uid in (' . $s . ')')->select('userstatic');
    foreach ($res as $key => $val) {
        $ret[$val['uid']] = $val;
    }
    return $ret;
}

/**
 * 批量获取主播等级
 * @param type $uids
 * @param type $db
 * @return type
 */
function getLevelByUid($uids, $userType, $db) {
    $alevels = array();
    $s = implode(',', $uids);
    if ($userType == 2) {
        $res = $db->field('uid,level')->where("uid in($s)")->order('level desc')->select('useractive');
    } else {
        $res = $db->field('uid,level')->where("uid in($s)")->order('level desc')->select('anchor');
    }
    if ($res) {
        foreach ($res as $v) {
            $alevels[$v['uid']] = $v;
        }
    }
    return $alevels;
}

/**
 * 检测主播是否在直播
 * @param type $luids
 * @param type $db
 */
function checkAuthoerIsLive($uids, $db) {
    $s = implode(',', $uids);
    $ret = array();
    $res = $db->field('uid,status')->where('status=' . LIVE . ' ' . '  and uid in (' . $s . ')')->select('live');
    foreach ($res as $key => $val) {
        $ret[] = $val['uid'];
    }
    return $ret;
}

/**
 * 获取主播等级
 * @param int $size
 * @param object $db
 * @param object $redobj
 * @return array
 */
function Anchorlevel($size, $db, $redobj) {
    $alevels = array();
    $res = $db->field('uid,level,integral')->order('level desc')->limit($size)->select('anchor');
    if ($res) {
        foreach ($res as $v) {
            $alevels[$v['uid']] = $v;
        }
    }
    return $alevels;
}

/**
 * 获取人气排行榜
 * @param int $userType
 * @param int $timeType
 * @param int $size
 * @param object $db
 * @param object $redobj
 * @return array
 */
function getAuthorPopularityRank($userType, $timeType, $size, $db, $redobj) {
    $Polist = array();
    if ($timeType == 1) {
        $beginTime = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d'), date('Y')));
        $endTime = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1);
    }
    if ($timeType == 2) {
        $thisweek = ThisWeekStartEnd();
        $beginTime = $thisweek['start'];
        $endTime = $thisweek['end'];
    }
    if ($timeType == 3) {
        $beginTime = date('Y-m-d H:i:s', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $endTime = date('Y-m-d H:i:s', mktime(23, 59, 59, date('m'), date('t'), date('Y')));
    }

    $res = $db->field('luid,sum(giftnum) as huancoin')->where("ctime >='$beginTime'  and ctime<='$endTime' group by luid")
                    ->order('huancoin DESC')->limit($size)->select('giftrecord');
    if ($res) {
        foreach ($res as $v) {
            $Polist[$v['luid']] = $v;
        }
    }
    return $Polist;
}

/**
 * 获取排行
 * @param int $userType
 * @param int $timeType
 * @param int $size
 * @param object $db
 * @param object $redobj
 * @param array $conf
 * @return array
 */
function getRanking($userType, $timeType, $orderType, $size, $db, $redobj, $conf) {
    $listIds = $newRes = $rankList = $rankListes = $todayUids = $yesterdayUids = array();
    if ($orderType == 1) {
        $res = getSalaryRank($userType, $timeType, $size, $db, $redobj);
        if ($res) {
            if ($userType == 1) {
                $param = 'beneficiaryid';
            }
            if ($userType == 2) {
                $param = 'customerid';
            }
            foreach ($res as $v) {
                $listIds[] = $v[$param];
            }
            if ($listIds) {
                $list = getUserPicAndNicks($listIds, $db);
                $authorStatus = array_unique(checkAuthoerIsLive($listIds, $db));
                $userLevel = getLevelByUid($listIds, $userType, $db);
                foreach ($res as $k => $v) {
                    $rankList['uid'] = $v[$param];
                    $rankList['anchorPicUrl'] = $list[$v[$param]]['pic'] ? "http://" . $conf['domain-img'] . '/' . $list[$v[$param]]['pic'] : DEFAULT_PIC;
                    $rankList['nick'] = $list[$v[$param]]['nick'];
                    $rankList['money'] = $v['coin'];
                    $rankList['level'] = array_key_exists($v[$param], $userLevel) ? $userLevel[$v[$param]]['level'] : 1;
                    if (in_array($v[$param], $authorStatus)) {
                        $rankList['isLive'] = 1;
                    } else {
                        $rankList['isLive'] = 0;
                    }
                    array_push($rankListes, $rankList);
                    array_push($todayUids, $v[$param]);
                }
            }
            if (($userType == 1 && $timeType == 1) || ($userType == 2 && $timeType == 1)) {
                for ($i = 0, $j = count($rankListes); $i < $j; $i++) {
                    $rankListes[$i]['status'] = '1';
                }
                $yesterdayrank = getYesterdayRank($userType, $timeType, $size, $db, $redobj);
                foreach ($yesterdayrank as $v) {
                    array_push($yesterdayUids, $v[$param]);
                }
                $existCommont = array_intersect($todayUids, $yesterdayUids);
                $diffkey = array_keys($existCommont);
                if ($existCommont) {
                    $yesterdayUids = array_flip($yesterdayUids);
                    for ($m = 0, $n = count($diffkey); $m < $n; $m++) {
                        $number = $existCommont[$diffkey[$m]];
                        $yesterdays = $yesterdayUids[$number];
                        if ($diffkey[$m] > $yesterdays) {
                            $rankListes[$diffkey[$m]]['status'] = '-1';
                        }
                        if ($diffkey[$m] == $yesterdays) {
                            $rankListes[$diffkey[$m]]['status'] = '0';
                        }
                    }
                }
            }
        } else {
            $rankListes = array();
        }
        return $rankListes;
    }
    //根据等级
    if ($orderType == 2) {
        $res = Anchorlevel($size, $db, $redobj);
        if ($res) {
            $uids = array_keys($res);
            $rankListes = $levelList = array();
            $userInfo = getUserPicAndNicks($uids, $db);
            $authorStatus = array_unique(checkAuthoerIsLive($uids, $db));
            foreach ($res as $k => $v) {
                $levelList['uid'] = $v['uid'];
                $levelList['anchorPicUrl'] = $userInfo[$v['uid']]['pic'] ? "http://" . $conf['domain-img'] . '/' . $userInfo[$v['uid']]['pic'] : '';
                $levelList['nick'] = $userInfo[$v['uid']]['nick'];
                $levelList['level'] = $v['level'];
                $levelList['money'] = $v['level']; //这里是为了前端便于显示所以这样组织的
                $levelList['integral'] = $v['integral'];
                if (in_array($v['uid'], $authorStatus)) {
                    $levelList['isLive'] = 1;
                } else {
                    $levelList['isLive'] = 0;
                }
                array_push($rankListes, $levelList);
            }
            //如果等级相同,贡献度大的排在前面
            foreach ($rankListes as $key => $value) {
                $money[$key] = $value['money'];
                $integral[$key] = $value['integral'];
            }
            array_multisort($money, SORT_NUMERIC, SORT_DESC, $integral, SORT_STRING, SORT_DESC, $rankListes);
        }
        return $rankListes;
    }
    //人气
    if ($orderType == 3) {
        $res = getAuthorPopularityRank($userType, $timeType, $size, $db, $redobj);
        if ($res) {
            $uids = array_keys($res);
            $rankListes = $levelList = array();
            $userInfo = getUserPicAndNicks($uids, $db);
            $authorLevel = getLevelByUid($uids, $userType, $db);
            $authorStatus = array_unique(checkAuthoerIsLive($uids, $db));
            foreach ($res as $k => $v) {
                $levelList['uid'] = $v['luid'];
                $levelList['anchorPicUrl'] = $userInfo[$v['luid']]['pic'] ? "http://" . $conf['domain-img'] . '/' . $userInfo[$v['luid']]['pic'] : '';
                $levelList['nick'] = $userInfo[$v['luid']]['nick'];
                $levelList['money'] = $v['huancoin'];
                $levelList['level'] = array_key_exists($v['luid'], $authorLevel) ? $authorLevel[$v['luid']]['level'] : 1;
                if (in_array($v['luid'], $authorStatus)) {
                    $levelList['isLive'] = 1;
                } else {
                    $levelList['isLive'] = 0;
                }
                array_push($rankListes, $levelList);
            }
            if (($userType == 1 && $timeType == 1)) {
                for ($i = 0, $j = count($rankListes); $i < $j; $i++) {
                    $rankListes[$i]['status'] = '1';
                }
                $yesterdayrank = getYesterdayPopularityRank($size, $db, $redobj);
                $existCommont = array_intersect($uids, $yesterdayrank);
                $diffkey = array_keys($existCommont);
                if ($existCommont) {
                    $yesterdayUids = array_flip($yesterdayrank);
                    for ($m = 0, $n = count($diffkey); $m < $n; $m++) {
                        $number = $existCommont[$diffkey[$m]];
                        $yesterdays = $yesterdayrank[$number];
                        if ($diffkey[$m] > $yesterdays) {
                            $rankListes[$diffkey[$m]]['status'] = '-1';
                        }
                        if ($diffkey[$m] == $yesterdays) {
                            $rankListes[$diffkey[$m]]['status'] = '0';
                        }
                    }
                }
            }
        } else {
            $rankListes = array();
        }
        return $rankListes;
    }
}

/**
 * start
 */
$userType = isset($_POST['userType']) ? (int) ($_POST['userType']) : 1;
$timeType = isset($_POST['timeType']) ? (int) ($_POST['timeType']) : 1;
$orderType = isset($_POST['orderType']) ? (int) ($_POST['orderType']) : 1;
$size = isset($_POST['size']) ? (int) ($_POST['size']) : 10;

$userType = checkInt($userType);
$timeType = checkInt($timeType);
$orderType = checkInt($orderType);
$size = checkInt($size);

$res = getRanking($userType, $timeType, $orderType, $size, $db, $redobj, $conf);
if ($res) {
    exit(jsone(array('rankList' => $res)));
} else {
    exit(jsone(array('rankList' => '')));
}


