<?php

/**
 * 已审核,未审核,未通过数量
 * date 2016-90-12 15:30
 * yandong@6rooms.com
 */
require_once '../../includeAdmin/init.php';
require '../../includeAdmin/Admin.class.php';
$db = new DBHelperi_admin();

/**
 * 待审核
 * @param type $db
 * @return string
 */
function getWaitPass($db) {
    $res = $db->field('count(*) as  total')->where('status=' . RN_WAIT)->select('userrealname');
    if ($res !== false && isset($res[0]['total'])) {
        $wait = $res[0]['total'];
    } else {
        $wait = '0';
    }
    return $wait;
}

/**
 * 已通过
 * @param type $db
 * @return string
 */
function getPass($db) {
    $res = $db->field('count(*) as  total')->where('status=' . RN_PASS)->select('userrealname');
    if ($res !== false && isset($res[0]['total'])) {
        $pass = $res[0]['total'];
    } else {
        $pass = '0';
    }
    return $pass;
}

/**
 * 未通过
 * @param type $db
 * @return string
 */
function getUnPass($db) {
    $res = $db->field('count(*) as  total')->where('status=' . RN_UNPASS)->select('userrealname');
    if ($res !== false && isset($res[0]['total'])) {
        $unpass = $res[0]['total'];
    } else {
        $unpass = '0';
    }
    return $unpass;
}

function getResult($db) {
    $wait = getWaitPass($db);
    $pass = getPass($db);
    $unpass = getUnPass($db);
    return array('wait' => $wait, 'pass' => $pass, 'unpass' => $unpass);
}

$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)($_POST['type']) : '1';//管理员类型
if (empty($uid) || empty($encpass)) {
    error(-1005);
}
if(!is_numeric($type)){
    error(-1023);
}
$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

$data = getResult($db);
succ($data);
