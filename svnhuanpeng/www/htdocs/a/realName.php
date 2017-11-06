<?php

include '../init.php';
/**
 * 实名认证
 * date 2016-1-11 11:09
 * author yandong@6rooms.com
 * version 0.0
 */
$db = new DBHelperi_huanpeng();

/**
 * start
 */
$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '0';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$papersid = isset($_POST['papersid']) ? trim($_POST['papersid']) : '';
$papersetime = isset($_POST['papersetime']) ? trim($_POST['papersetime']) : '';
$face = isset($_POST['face']) ? trim($_POST['face']) : '';
$back = isset($_POST['back']) ? trim($_POST['back']) : '';
$handheldPhoto = isset($_POST['handheldPhoto']) ? trim($_POST['handheldPhoto']) : '';
$paperstype = isset($_POST['paperstype']) ? trim($_POST['paperstype']) : '';
if (empty($uid) || empty($encpass) || empty($name) || empty($papersid) || empty($papersetime) || empty($face) || empty($back) || empty($$handheldPhoto) || empty($paperstype)) {
    error(-993);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$name = filterData($name);
$papersid = filterData($papersid);
$paperstype = filterData($paperstype);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error($s);
}
//验证证件的合法性
if ($paperstype == 1) {
    $checkIDC = checkIDCard($papersid);
    if (!$checkIDC) {
        error(-988);
    }
}

function addRealName($uid, $name, $papersid, $papersetime, $face, $back, $handheldPhoto, $paperstype, $db) {
    $data = array(
        'papersid' => $papersid,
        'papersetime' => $papersetime,
        'face' => $face,
        'back' => $back,
        'handheldPhoto' => $handheldPhoto,
        'uid' => $uid,
        'ctime' => date('Y-m-d H:i:s'),
        'status' => 0,
        'paperstype' => $paperstype
    );
    $res = $db->insert('userrealname', $data);
    return $res;
}

$rest = addRealName($uid, $name, $papersid, $papersetime, $face, $back, $handheldPhoto, $paperstype, $db);
if ($rest) {
    $isSuccess = 1;
} else {
    $isSuccess = 0;
}
exit(jsone(array('isSuccess' => $isSuccess)));
