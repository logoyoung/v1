<?php

include '../../../../include/init.php';
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
use service\user\UserAuthService;

/**
 * 我的关注暂未开播列表
 * date 2015-12-14 17:58
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 关注
 * @param int $uid
 * @param int $page
 * @param int $size
 * @param object $db
 * @return array
 */
function seachfollowUser($uid, $db) {
    $rows = '';
//    $count = $db->field('count(uid2) as ucount')->where('uid1 =' . $uid . '')->select('userfollow');
    $row = $db->field('uid2')->where('uid1 =' . $uid . '')->select('userfollow');
    if ($row) {
        foreach ($row as $v) {
            $rows[$v['uid2']] = $v['uid2'];
        }
    }
    return array('rows' => $rows ? $rows : array());
}

/**
 * 去除正在直播的主播
 * @param string $luid
 * @param object $db
 * @return array
 */
function getLiveInfo($luid, $db) {
    $row = $db->field('uid')->where("uid in($luid) and status =" . LIVE)->select('live');
    if ($row) {
        foreach ($row as $v) {
            $rows[$v['uid']] = $v['uid'];
        }
//        $res=  implode(',',array_diff(explode(',',$luid),$rows));
        return $rows;
    } else {
        return array();
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
$size = isset($_POST['size']) ? (int) $_POST['size'] : 3;
if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$size = checkInt($size);

$auth = new UserAuthService();
$auth->setUid($uid);
$auth->setEnc($encpass);
//校验encpass、用户 登陆状态
if($auth->checkLoginStatus() !== true)
{
    //获取校验结果
    $result    = $auth->getResult();
    //错误码
    $errorCode = $result['error_code'];
    //错误消息
    $errorMsg  = $result['error_msg'];
    //假如是封禁的，可以获取禁时间
    $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
    write_log("notice|uid:{$uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|api:".__FILE__,'auth_access');
    error2(-4067,2);
}

$trows = seachfollowUser($uid, $db);
if ($trows['rows']) {
    $outLine = getLiveInfo(implode(',', array_keys($trows['rows'])), $db);
    if ($outLine) {
        $trows['rows'] = array_diff($trows['rows'], $outLine); //未直播
    }
    if ($trows['rows']) {
        $fans = batchGetFansCount(implode(',', $trows['rows']), $db); //获取关注人数
        $trows['count'] = count($trows['rows']);
        $afterSort = $follows = $follow = array();
        foreach ($trows['rows'] as $rowk => $rowv) {
            $userinfo = getUserinfo($rowv, $db);
            $follow['uid'] = $userinfo[0]['uid'];
            $follow['head'] = ($userinfo[0]['pic']) ? (DOMAIN_PROTOCOL . $conf['domain-img'] . '/' . $userinfo[0]['pic']) : DEFAULT_PIC;
            $follow['nick'] = $userinfo[0]['nick'];
            $follow['fansCount'] = array_key_exists($rowv, $fans) ? $fans[$rowv] : 0;
            array_push($follows, $follow);
        }
        $afterSort = dyadicArray($follows, 'fansCount'); //根据关注数量排序
        $page = returnPage($trows['count'], $size, $page);
        $offect = ($page - 1) * $size;
        $number = array_slice($afterSort, $offect, $size); //以后加缓存
    } else {
        $number = array();
    }
} else {
    $number = array();
}
if (!empty($number)) {
    succ(array('list' => $number, 'total' => $trows['count']));
} else {
    succ(array('list' => array(), 'total' => '0'));
}


