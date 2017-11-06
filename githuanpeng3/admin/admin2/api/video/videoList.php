<?php

/**
 * 获取一条待审核录像信息
 * yandong@6rooms.com
 * date 2016-06-30 16:25
 * 
 */
require '../../includeAdmin/Video.class.php';
require '../../includeAdmin/Admin.class.php';
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_admin();

/**
 * 字符串截取
 * @param type $str
 * @param type $len
 * @param type $suffix
 * @return type
 */
function Cut($str, $len, $suffix = true) {
    if (mb_strlen($str, 'utf8') > $len) {
        if ($suffix) {
            return mb_substr($str, 0, $len, 'utf8') . '....';
        } else {
            return mb_substr($str, 0, $len, 'utf8');
        }
    }
    return $str;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int) $_POST['type'] : '';
if (empty($uid) || empty($encpass) || empty($type)) {
    error(-4013);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$videoObj = new Video();
$isLockVideo = $videoObj->getLockVideo($uid);
if ($isLockVideo) {
    $res = $videoObj->getVideoInfo($isLockVideo);
} else {
    $videoid = $videoObj->getNewVideo();
    if ($videoid) {
        $status = $videoObj->setVideoLock($uid, $videoid);
        if (true === $status) {
            $res = $videoObj->getVideoInfo($videoid);
        }
    } else {
        $res = array();
    }
}
if ($res) {
    $liveids = array_column($res, 'liveid');  
    $live = $videoObj->getLiveTime($liveids);
    $url = "http://" . $conf['domain-img'] . '/';
    foreach ($res as $v) {
        $anchorInfo = getUserInfo($v['uid'], $db);
        $data['nick'] = $anchorInfo[0]['nick'];
        $data['pic'] = $anchorInfo[0]['pic'] ? $url . $anchorInfo[0]['pic'] : DEFAULT_PIC;
        if ($v['gametid']) {
            $data['gametype'] = getGameTypeName($v['gametid'], $db);
        } else {
            $data['gametype'] = '其他';
        }
        $data['gamename'] = $v['gamename'];
        $data['orientation'] = $v['orientation'];
        $data['title'] = Cut($v['title'], 8, $suffix = true);
        $data['ctime'] = $v['ctime'];
        $data['livetime'] = $live[$v['liveid']];
        $data['vid'] = $v['videoid'];
        $data['length'] = SecondFormat($v['length']);
        $data['poster'] = sposter($v['poster']);
        $data['file'] = sfile($v['vfile']);
    }
    succ($data);
} else {
    error();
}





