<?php

/**
 * 添加主播到待推荐列表
 * yandong@6rooms.com
 * date 2016-11-19 14:52
 *
 */
require '../../../includeAdmin/init.php';
require INCLUDE_DIR . "Admin.class.php";
$db = new DBHelperi_admin();

/**根据昵称或uid 获取主播信息
 * @param  int $type 1:昵称  2:uid
 * @param  string $param 昵称｜｜ uid
 * @param $db
 * @return bool
 */
function getAnchorInfoByNick($type, $param, $db)
{
    if ($type == 1) {//按昵称
        $where = "nick='$param'";
    }
    if ($type == 2) {//按UID
        $where = "uid=$param";
    }
    $res = $db->field('uid,nick,pic')->where($where)->select('userstatic');
    if (false !== $res) {
        return $res;
    } else {
        return false;
    }
}

/*添加数据到待推荐主播表
 * @param $luid  主播id
 * @param $nick  主播昵称
 * @param $head   主播头像
 * @param $db
 * @return bool
 */
function addToWiatRecommentList($uid, $adminid, $nick, $head, $db)
{
    if (empty($uid) || empty($nick)) {
        return false;
    }
    $sql = "insert into admin_recommend_live (`uid`,`nick`,`head`,`adminid`) value($uid,'$nick','$head',$adminid) on duplicate key update uid=$uid,nick='$nick',head='$head',adminid=$adminid,status=0";
    $res = $db->query($sql);
    if (false !== $res) {
        return $res;
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
$addType = isset($_POST['searchType']) ? (int)$_POST['searchType'] : 1;
$param = isset($_POST['nick']) ? trim($_POST['nick']) : '';

if (empty($uid) || empty($encpass) || empty($param)) {
    error(-1007);
}
if ($addType == 2) {
    if (preg_match("/[^\d-., ]/", $param)) {
        error(-1019);
    }
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$anchorinfo = getAnchorInfoByNick($addType, $param, $db);
if ($anchorinfo) {
    if (count($anchorinfo) > 1) {
        error(-1017);
    }
} else {
    error(-1018);
}
$luid = $anchorinfo[0]['uid'];
$nick = $anchorinfo[0]['nick'];
$head = $anchorinfo[0]['pic'];
$result = addToWiatRecommentList($luid, $uid, $nick, $head, $db);
if (false !== $result) {
    succ(array('luid' => $anchorinfo[0]['uid']));
} else {
    error(-1014);
}