<?php

include '../init.php';
require(INCLUDE_DIR.'User.class.php');
$db = new DBHelperi_huanpeng();

/**
 * 用户头像状态
 * @param int $uid
 * @param obj $db
 * @return string
 */
function userPicStatus($uid, $db) {
    $res = $db->field('status')->where("uid=$uid")->select('admin_user_pic');
    if (empty($res)) {
        $status = '1';
    } else {
        if ($res[0]['status'] == 0) {
            $status = '0'; //审核中
        }
        if ($res[0]['status'] == 1) {
            $status = '1'; //已通过
        }
        if ($res[0]['status'] == 2) {
            $status = '2'; //未通过
        }
    }
    return $status;
}

$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
if (!$uid || !$enc)
    exit(json_encode(array('code' => -1, 'desc' => '参数错误')));

$code = checkUserState($uid, $enc, $db);
if ($code !== true)
    exit($code);

$user = getUserBaseInfo($uid, $db);

$sql = "select * from userlevel where level = {$user['level']}";
$res = $db->query($sql);
$row = $res->fetch_assoc();

$user['levelIntegral'] = $row['integral'];
$user['anchorLevel'] = getAnchorLevel($uid, $db);
$user['picCheckStat'] = userPicStatus($uid, $db);
$user['picCheckUrl'] = 'http://' . $conf['domain'] . "/main/a/server/getUserHead.php?".http_build_query(array('time'=>time(), 'uid'=>$uid, 'enc'=>$enc));//"time=" . time() ."";
$url = "http://" . $conf['domain-img'] . '/';
$user['pic'] = $user['pic'] ? $url . $user['pic'] : DEFAULT_PIC;
$user['uid'] = "$uid";
$fansCount = getFansCount($uid, $db);
$user['fans'] = $fansCount ? "$fansCount" : "0";
$userobj = new UserHelp($uid, $db);
$realName = $userobj->getRealNameCertifyInfo();
if ($realName['status'] == RN_PASS) {
    $user['isAnchor'] = '1';
} else {
    $user['isAnchor'] = '0';
}
echo json_encode($user);
