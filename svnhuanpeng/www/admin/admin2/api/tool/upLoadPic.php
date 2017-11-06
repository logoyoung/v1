<?php
/**
 * 上传图片并返回图片地址
 * yandong@6rooms.com
 * date 2016-07-05 10:16
 */
require '../../includeAdmin/init.php';
require '../../includeAdmin/upload.class.php';
require '../../includeAdmin/Admin.class.php';
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$pathConfig = array('game', 'information', 'active'); //0游戏 1资讯 2活动
/**
 * start
 */
$utype = isset($_POST['utype']) ? (int)$_POST['utype'] : 0;
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
if (!in_array($utype, array(0, 1, 2))) {
    error(-1023);
}
if ($utype == 0) {
    $path = '/' . $pathConfig[$utype] . '/';
}
if ($utype == 1) {
    $path = '/' . $pathConfig[$utype] . '/' . date('Y', time()) . '/' . date('m', time()) . '/';
}
if ($utype == 2) {
    $path = '/' . $pathConfig[$utype] . '/' . date('Y', time()) . '/' . date('m', time()) . '/';
}
$upObj = new UpLoad($conf['img-dir'] . $path);
$picUrl = $upObj->exec($_FILES['file']); // 上传文件
$url = "http://" . $conf['domain-img'] . '/';
if ($picUrl) {
    if ($utype == 0) {
        exit(json_encode(array('code' => 1, 'data' => $path . $picUrl[0])));
    } else {
        succ(array('domain'=>$url,'poster' => $path . $picUrl[0]));
    }
} else {
    if ($utype == 0) {
        exit(json_encode(array('code' => 0, 'data' => $path . $picUrl[0])));
    } else {
        succ(array('domain'=>$url,'poster'=>$path . $picUrl[0]));
    }

}




