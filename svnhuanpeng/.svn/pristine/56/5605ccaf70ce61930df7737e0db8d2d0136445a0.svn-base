<?php
/**
 * 添加主播到推荐列表
 * yandong@6rooms.com
 * date 2016-11-21 10:52
 *
 */
require '../../../includeAdmin/init.php';
require INCLUDE_DIR . "Admin.class.php";
$db = new DBHelperi_admin();


/**获取已推荐列表
 * @param $client 1、app 2 web
 * @param $db
 * @return array|bool
 */
function getRecommentList($client, $db)
{
    if (empty($client)) {
        return false;
    }
    $res = $db->field('list')->where("client=$client")->select('recommend_live');
    if (false !== $res && !empty($res)) {
        return $res[0]['list'];
    } else {
        return array();
    }

}


/**添加数据到recomment_live表
 * @param $client 平台
 * @param $luid  主播
 * @param $db
 * @return bool
 */

function addToRecommentList($client, $luid, $db)
{
    if (empty($client) || empty($luid)) {
        return false;
    }
    $utime = date('Y-m-d H:i:s', time());
    $sql = "insert into recommend_live (`client`,`list`,`utime`) value($client,'$luid','$utime') on duplicate key update list='$luid',utime='$utime'";
    $res = $db->query($sql);
    if (false !== $res) {
        return $res;
    } else {
        return false;
    }

}

/**修改带推荐表中的状态
 * @param $uid  主播id
 * @param $db
 * @return bool
 */
function changeWaitListStatus($uid, $db)
{
    if (empty($uid)) {
        return false;
    }
    $res = $db->where("uid=$uid")->update('admin_recommend_live', array('status' => 1));
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

/**校验要添加的主播是否在待添加列表中
 * @param $uids  主播列表
 * @param $db
 * @return array|bool
 */
function checkExistWaitList($uids, $db)
{
    if (empty($uids)) {
        return false;
    }
    $res = $db->field('uid')->where("uid in ($uids)")->select('admin_recommend_live');
    if (false !== $res && !empty($res)) {
        return $res;
    } else {
        return array();
    }
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : 1;
$luid = isset($_POST['luid']) ? trim($_POST['luid']) : '';
$client = isset($_POST['client']) ? (int)$_POST['client'] : 2;


if (empty($uid) || empty($encpass) || empty($luid)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$new = array_filter(explode(',', $luid));
if (count($new) > LIVE_RECOMMENT_NUMBER) {
    error(-1022);
}
$checkres = checkExistWaitList($luid, $db);
if (empty($checkres)) {
    error(-1021);
}
$recomment = getRecommentList($client, $db);
if ((int)count(array_filter(explode(',', $recomment))) >= LIVE_RECOMMENT_NUMBER) {
    error(-1020);
}
if (((int)count($new)) + (int)count( array_filter(explode(',', $recomment))) > LIVE_RECOMMENT_NUMBER) {
    error(-1022);
}

$luid = implode(',', array_intersect(array_column($checkres, 'uid'), $new));
if ($recomment) {
    $luids = $recomment . ',' . $luid;
} else {
    $luids = $luid;
}
$isfull = array_filter(explode(',', $luids));
if (count($isfull) > (int)LIVE_RECOMMENT_NUMBER) {
    error(-1035);
}
$result = addToRecommentList($client, $luids, $db);
if (false !== $result) {
    $res = changeWaitListStatus($luid, $db);
    if ($res) {
        succ();
    } else {
        error(-1014);
    }
} else {
    error(-1014);
}