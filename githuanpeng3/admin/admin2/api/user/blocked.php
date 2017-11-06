<?php
/**
 * 封号
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
 * 封号----暂时把password字段置空
 * @param type $db
 * @param int $uid  管理员id
 * @param int $luid 用户id
 * @return boolean
 */
function blocked($db, $uid, $luid, $reason) 
{
	$insert = array(
        'uid' => $uid,
        'luid' => $luid,
        'reason' => $reason,
    );
	$db->insert('userblockedlist', $insert);
	
    $where = '`uid`=' . $luid;
    $update = array(
        'password' => '',
    );
    
    return $db->where($where)->update('userstatic', $update);
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
$reason = isset($_POST['reason']) ? $_POST['reason'] : '';
if(!$luid) {
    error(-1023);
}
$res = blocked($db, $uid, $luid, $reason);
if ($res) {
    succ();
} else {
    error(-1014);
}
