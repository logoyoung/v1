<?php

/**
 * 取消推荐
 * yandong@6rooms.com
 * date 2016-11-21 19:02
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
    if (empty($client)) {
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
    $res = $db->where("uid in ($uid)")->update('admin_recommend_live', array('status' => 0));
    if (false !== $res) {
        return true;
    } else {
        return false;
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
if ($luid) {
    if (preg_match("/[^\d-., ]/", $luid)) {
        error(-1019);
    }
}
if(!in_array($client,array(2))){
    error(-1023);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$recomment = getRecommentList($client, $db);
$olderList=explode(',',$recomment);
$delList=explode(',',$luid);
$diff=array_diff($olderList,$delList);
$same=array_intersect($delList,$olderList);
$result = addToRecommentList($client, implode(',',$diff), $db);
if (false !== $result) {
    $res = changeWaitListStatus(implode(',',$same), $db);
    if ($res) {
        succ();
    } else {
        error(-1014);
    }
} else {
    error(-1014);
}