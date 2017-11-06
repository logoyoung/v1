<?php

/**
 * 重新审核头像
 * yandong@6rooms.com
 * date 2016-11-15 11：11
 *
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();

/**
 * @param $succluid  string
 * @param $db
 * @return bool
 */
function UpdatePicStatus($succluid, $db)
{
    if (empty($succluid)) {
        return false;
    }
    $update = $db->where("uid in ($succluid) ")->update('admin_user_pic', array('status' => USER_PIC_WAIT, 'utime' => date('Y-m-d H:i:s', time()))); //修改审核状态通过
    if ($update !== false) {
        $tostatic = $db->where("uid in ($succluid)")->update('userstatic', array('pic' => DEFAULT_HEAD_PATH));
        if ($tostatic !== false) {
            return true;
        } else {
            return false;
        }
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
$succluid = isset($_POST['succuid']) ? trim($_POST['succuid']) : ''; //主播id列表批量可用逗号隔开（重新审核的)

if (empty($uid) || empty($encpass) || empty($type) || empty($succluid)) {
    error(-1007);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}
$res = UpdatePicStatus(trim($succluid,','), $db);
if ($res) {
    succ();
} else {
    error(-1014);
}
