<?php

include '../init.php';
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
/**
 * 我的关注列表
 * date 2015-12-14 17:58
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * 获取直播信息
 * @param int $luid
 * @param int $db
 * @return array
 */
function getLiveInfo($luid, $db) {
    $row = $db->field('uid, title,gamename,poster, orientation,ctime,status')->order('liveid DESC')->where("uid =$luid")->limit('1')->select('live');
    return $row;
}

/**
 * 关注
 * @param int $uid
 * @param int $page
 * @param int $size
 * @param object $db
 * @return array
 */
function seachfollowUser($uid, $db) {
    $count = $db->field('count(uid2) as ucount')->where('uid1 =' . $uid . '')->select('userfollow');
    $rows = $db->field('uid2')->where('uid1 =' . $uid . '')->order('tm desc')->select('userfollow');
    return array('rows' => $rows ? $rows : array(), 'count' => $count ? $count[0]['ucount'] : 0);
}

/**
 * 二维数组排序
 * @param array $multi_array 待排序的数组
 * @param string $sort_key   要排序的字段
 * @param string $sort       排序的规则
 * @return array
 */
function ArraySort($multi_array, $sort_key, $sort = SORT_DESC ) {
    if (is_array($multi_array)) {
        foreach ($multi_array as $row_array) {
            if (is_array($row_array)) {
                $key_array[] = $row_array[$sort_key];
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
    array_multisort($key_array, $sort, $multi_array);
    return $multi_array;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '0';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$client = isset($_POST['client']) ? (int) $_POST['client'] : '';
$page = isset($_POST['page']) ? (int) $_POST['page'] : 1;
$size = isset($_POST['size']) ? (int) $_POST['size'] : 3;
if (empty($uid) || empty($encpass)) {
    error(-993);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$size = checkInt($size);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}

$trows = seachfollowUser($uid, $db);
$living = $unliving = $follow = array();
if ($trows['rows']) {
    foreach ($trows['rows'] as $rowk => $rowv) {
        if ($followNub = getLiveInfo($rowv['uid2'], $db)) {
            foreach ($followNub as $fk => $fv) {
                $userinfo = getUserinfo($fv['uid'], $db);
                $follow['anchorUserID'] = $fv['uid'];
                $follow['liveStartTime'] = strtotime($fv['ctime']);
                $follow['liveTitle'] = $fv['title'];
                $follow['gamename'] = $fv['gamename'];
                $follow['poster'] = ($fv['poster']) ? ("http://" . $conf['domain-img'] . '/' . $fv['poster']) : '';
                $follow['anchorPicURL'] = ($userinfo[0]['pic']) ? ("http://" . $conf['domain-img'] . '/' . $userinfo[0]['pic']) : DEFAULT_PIC;
                $follow['anchorNickName'] = $userinfo[0]['nick'];
//                $follow['viewerCount'] = getLiveRoomUserCount($rowv['uid2'], $db);
                $follow['viewerCount'] = getFansCount($rowv['uid2'], $db);                
                if ($fv['status'] == LIVE) {
                    $follow['liveStatus'] = '1';
                    $follow['angle']=$fv['orientation'];
                    array_push($living, $follow);
                } else {
                    $follow['liveStatus'] = '0';
                    array_push($unliving, $follow);
                }
            }
        }
    }
} else {
    exit(jsone(array('followList' => array(), 'liveCount' => '0', 'allCount' =>'0')));
}
$list = $living ? $living : array();
if ($client == 1 && empty($list)) {
    exit(jsone(array('followList' => array(), 'liveCount' => '0', 'allCount' => '0')));
} else {
    $page=returnPage($trows['count'], $size, $page);
    $offect = ($page - 1) * $size; //偏移量
    if ($client == 1) {
        if (count($list) > 1) {
            $list = ArraySort($list, 'liveStartTime');
        }
        $followlist = array_slice($list, $offect, $size); //以后加缓存
    } else {
        if (count($list) > 1) {
            $list = ArraySort($list, 'liveStartTime');
        }
//        var_dump($list);exit;
        $unlist = array_merge($list, $unliving);
        $followlist = array_slice($unlist, $offect, $size); //以后加缓存
    }
}
if (!empty($followlist)) {
    exit(jsone(array('followList' => $followlist, 'liveCount' => count($living), 'allCount' => $trows['count'])));
} else {
    exit(jsone(array('followList' => array(), 'liveCount' => '0', 'allCount' => '0')));
}


