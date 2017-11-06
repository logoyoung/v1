<?php
/**
 * 删除禁言
 * jiantao@6.cn
 * date 2016-10-12 13:55
 * 
 */
ini_set('display_errors', 'On');
error_reporting(E_ALL);
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
require '../../includeAdmin/Redis.class.php';
$db = new DBHelperi_admin();

/**
 * 删除禁言
 * @param type $db
 * @param int $uid  管理员id
 * @param int $luid 用户id
 * @param int $roomid 禁言房间号
 * @param 1 $type 类型 1禁言操作 2已取消 
 * @return boolean
 */
function deleteSilence($db, $uid, $luid, $roomid, $type) 
{
    $date = date('Y-m-d H:i:s');
    $where = '`luid`=' . $luid . ' and `roomid`=' . $roomid . ' and `type`=1';
    $data = array(
        'uuid' => $uid,
        'utime' => $date,
        'type' => $type
    );
    
    return $db->where($where)->update('usersilence', $data);
}

/**
 * start
 */

    $uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
    $encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
    $type = isset($_POST['type']) ? (int) $_POST['type'] : 1;
    if (empty($uid) || empty($encpass) || empty($type)) {
        error(-1007);
    }
    
    $adminHelp = new AdminHelp($uid, $type);
    $err = $adminHelp->loginError($encpass);
    if ($err) {
        error($err);
    }

$luid = isset($_POST['luid']) ? (int) $_POST['luid'] : 0;
if(!$luid || !isset($_POST['roomid'])) {
    error(-1023);
}
$roomid = isset($_POST['roomid']) ? (int) $_POST['roomid'] : 0;

$redis = new RedisHelp();
$redis->hdel('silenced_' . $roomid, $luid);
$res = deleteSilence($db, $uid, $luid, $roomid, 2);
if ($res) {
    succ();
} else {
    error(-1014);
}
