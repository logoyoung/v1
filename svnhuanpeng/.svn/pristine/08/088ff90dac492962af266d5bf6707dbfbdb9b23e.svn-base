<?php

include '../init.php';


/**
 * 关注用户
 * 根据输入的目标用户ID，关注目标用户
 * @auth hantong<hantong@6rooms.com>
 * revise  by yandong at the time 2016-1-22 10:30
 * @version $ID$
 */
$db = new DBHelperi_huanpeng();

/**
 * 添加关注
 * @param int $uid
 * @param int $targetUserID
 * @param object $db
 * @return type
 */
function setFollow($uid, $targetUserID, $db) {
    $sql = "INSERT INTO `userfollow` (`uid1`, `uid2`) VALUES ($uid, $targetUserID) on duplicate key update uid1 = $uid, uid2 = $targetUserID";
    $res = $db->doSql($sql);
    return $res;
}

/**
 * 获取用户关注的主播数
 * @param int $uid
 * @param object $db
 * @return array
 */
function userFollowCount($uid, $db) {
    if(empty($uid)){
        return false;
    }
    $rows = $db->field('uid2')->where('uid1 =' . $uid . '')->limit(5)->select('userfollow');
    return $rows;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$targetUserID = isset($_POST['targetUserID']) ? (int) $_POST['targetUserID'] : '';

if (!$uid || !$encpass) {
    error('-1015');
}
if (!$targetUserID) {
    error('-1026');
}
if ($uid == $targetUserID) {
    error('-1027');
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$targetUserID = checkInt($targetUserID);

//检查用户登陆状态
$userState = checkUserState($uid, $encpass, $db);
if (true !== $userState) {
    error($userState);
}
//检查目标用户是否存在
$checkres = checkUserIsExist($targetUserID, $db);
if (empty($checkres)) {
    error('-1025');
}
$setres = setFollow($uid, $targetUserID, $db);
if (empty($setres)) {
    error('-1007');
}
addLiveNotice($uid, $targetUserID, $db); //同步到live_notice表中
$redobj = new RedisHelp();
$isOver = "FOLLOWUSER_OVER_$uid";
if ($redobj->isExists($isOver) === false) {//同步关注5个主播的奖励
    $resu = userFollowCount($uid, $db);
    if (count($resu) == 5) {
        $redobj->set($isOver, 1);
        synchroTask($uid, 18, 0, 100, $db);
    }
}
exit(jsone(array('isSuccess' => '1', 'targetUserID' => "$targetUserID")));
