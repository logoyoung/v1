<?php

include '../init.php';
require(INCLUDE_DIR . 'Anchor.class.php');
require(INCLUDE_DIR . 'LiveRoom.class.php');
/*
 * App直播结束
 * date 2016-04-14 10:30 
 * author yandong@6rooms.com
 * version 0.0
 */
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**
 * 获取直播结束后数据
 * @param type $uid
 * @param type $liveId
 * @param type $redisobj
 * @param type $db
 */
function getEndInfo($uid, $liveId, $conf, $db) {
    $LiveRoom = new LiveRoom($uid);
    $AnchorHelp = new AnchorHelp($uid);
    $coin = $AnchorHelp->receiveCoinCount($liveId);
    $bean = $AnchorHelp->receiveBeanCount($liveId);
    $authorInfo = $AnchorHelp->getUsers();
    $longTime = getLiveLongTime($uid, $liveId, $db);
    if ($longTime) {
        $time = timediff(strtotime($longTime[0]['ctime']), strtotime($longTime[0]['etime']));
    } else {
        $time = timediff(time(), time());
    }
    $level = $AnchorHelp->getLevelInfo();

//    $onlineCount = getLiveRoomUserCount($uid, $db);
    $followCount = $AnchorHelp->followCount();
    $isCertify = $AnchorHelp->getCertifyInfo();
    $livePake = $LiveRoom->getLiveCountPeakValue();
    $list['roomId'] = $uid;
    $list['liveId'] = $liveId;
    $list['nick'] = $authorInfo['nick'] ? $authorInfo['nick'] : '';
    $list['pic'] = $authorInfo['pic'] ? $authorInfo['pic'] : DEFAULT_PIC;
    $list['follow'] = $followCount;
    if ($time['days'] !== '0') {
        $list['livelong'] = $time['days'] . '天';
        if ($time['hour']) {
            $list['livelong'].= ($time['hour'] < 10 ? $time['hour'] . '小时' : $time['hour'] . '小时');
        }
    } else {
        if ($time['hour'] !== '0') {
            $list['livelong'] = ($time['hour'] < 10 ? '0' . $time['hour'] : $time['hour']) . ':' . ($time['min'] < 10 ? '0' . $time['min'] : $time['min']) . ':' . ($time['sec'] < 10 ? '0' . $time['sec'] : $time['sec']);
        } else {
            $list['livelong'] = '00:' . ($time['min'] < 10 ? '0' . $time['min'] : $time['min']) . ':' . ($time['sec'] < 10 ? '0' . $time['sec'] : $time['sec']);
        }
    }

    $list['peak'] = $livePake;
    $list['coin'] = $coin ? $coin : '0';
    $list['bean'] = $bean ? $bean : '0';
    $list['level'] = $level['level'] ? $level['level'] : 1;
    if ($isCertify['emailstatus'] == EMAIL_PASS && $isCertify['identstatus'] == RN_PASS && $isCertify['phonestatus'] == 1) {
        $list['isCertify'] = '1';
    } else {
        $list['isCertify'] = '0';
    }
    $getLimit = getAuchorVideoLimit($uid, $db); //获取发布录数
    $getpublish = getAnchorAlreadyPublishVideo($uid, $db); //获取已发布的录像数
    if ((int) $getpublish >= (int) $getLimit) {
        $list['autoFull'] = '0';
    } else {
        $list['autoFull'] = '1';
    }
    return $list;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$liveId = isset($_POST['liveId']) ? (int) ($_POST['liveId']) : '';
if (empty($uid) || empty($encpass) || empty($liveId)) {
    error(-993);
}
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}

$res = getEndInfo($uid, $liveId, $conf, $db);
if ($res) {
    exit(jsone(array('info' => $res)));
} else {
    exit(jsone(array('info' => array())));
}

