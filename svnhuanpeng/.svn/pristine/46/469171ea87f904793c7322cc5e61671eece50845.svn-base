<?php

/**
 * App 端意见反馈
 * date 2016-05-30 11:01
 * author yandong@6rooms.com
 */
include '../../../include/init.php';
$db = new DBHelperi_huanpeng();

/**
 * 添加一条意见反馈
 * @param type $uid  用户id
 * @param type $comment  意见/建议
 * @param type $contact  联系方式
 * @return boolean
 */
function addfeedBack($uid, $comment, $contact, $db) {
    if (empty($uid) || empty($comment) || empty($contact)) {
        return false;
    }
    $data = array(
        'uid' => $uid,
        'feedback' => json_encode(array('comment' => $comment, 'contact' => $contact))
    );
    $res = $db->insert('feedback', $data);
    return $res;
}

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
$contact = isset($_POST['contact']) ? trim($_POST['contact']) : '';

if (empty($uid) || empty($encpass)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);

if (empty($comment)) {
    error2(-4053,2);
}
$comment = filterData($comment);
if ($length = mb_strlen($comment,'utf8') > 200) {
    error2(-4052,2);
}
if (empty($contact)) {
    error2(-4051,2);
}
$checkphone = checkMobile($contact); //电话号码
$checkmail = checkEmailFormat($contact); //检测邮箱
$checkQQ = CheckQQ($contact);
if ($checkphone !== true && $checkmail !== true && $checkQQ !== true) {
    error2(-4051,2);
}
//检查用户登陆状态
$userState = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $userState) {
    error2(-4067,2);
}
$res = addfeedBack($uid, $comment, $contact, $db);
succ();