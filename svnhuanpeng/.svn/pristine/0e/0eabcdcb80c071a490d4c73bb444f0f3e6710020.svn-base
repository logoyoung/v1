<?php

include '../init.php';
/**
 * 取消关注用户接口
 * 根据输入的目标用户ID，取消对用户的关注，成功返回目标用户ID以及成功标志，失败返回错误信息
 * @auth hantong<hantong@6rooms.com>
 * revise  by yandong at the time 2016-1-22 10:30
 * @version $ID$
 */
$db = new DBHelperi_huanpeng();

/**
 * 取消关注
 * @param int $uid
 * @param int $targetUserID
 * @param object $db
 * @return bool
 */
function cancelFellow($uid, $targetUserID, $db) {
    $dres = $db->where("uid1=$uid AND uid2 in ($targetUserID)")->delete('userfollow');
    return $dres;
}

/**
 * 校验
 * @param type $uid
 * @param type $targetUserID
 * @return type
 */
function ChecktargetUserId($uid, $targetUserID) {
    $arr = explode(',', $targetUserID);
    $new = array();
    for ($i = 0, $k = count($arr); $i < $k; $i++) {
        if ($uid == $arr[$i]) {
            error('-1028');
        }
        if ($arr[$i]) {
            array_push($new, $arr[$i]);
            $res = implode(',', $new);
        }
    }
    return $res ? $res : '';
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$targetUserID = isset($_POST['targetUserID']) ? trim($_POST['targetUserID']) : '';
if (!$uid || !$encpass) {
    error('-1015');
}
if (!$targetUserID) {
    error('-1026');
}

$uid = checkInt($uid);
$encpass = checkStr($encpass);
$targetUserID = ChecktargetUserId($uid, $targetUserID);
//检查用户登陆状态
$userState = checkUserState($uid, $encpass, $db);
if ($userState !== true) {
    error($userState);
}
//检查目标用户是否存在
//$checkres = checkUserIsExist($targetUserID, $db);
//if (empty($checkres)) {
//    error('-1025');
//}
$cancelres = cancelFellow($uid, $targetUserID, $db);
if ($cancelres) {
    deleteLiveNotice($uid,$targetUserID,$db);//取消关注的同时,删掉live_notice表中的记录
    exit(jsone(array('isSuccess' => '1', 'targetUserID' => "$targetUserID")));
} else {
    exit(jsone(array('isSuccess' => '0', 'targetUserID' => "$targetUserID")));
}

