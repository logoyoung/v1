<?php
session_start();
/**
 * 上传图片
 */
include '../../../include/init.php';
require(INCLUDE_DIR . 'upload.class.php');
require(INCLUDE_DIR . 'Img.class.php');
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();
$pathConfig = array('report');//路径配置数组 可按不同地方放到不同目录下  type=0为上传举报截图

/*
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? $_POST['encpass'] : '';
$ptype = isset($_POST['type']) ? (int)$_POST['type'] : 0;
if (!$uid || !$encpass) {
    error2(-4013);
}
if(!in_array($ptype,array(0,1))){
    error2(-4026,2);
}
//检查用户登陆状态
$userState = checkUserState($uid, $encpass, $db);
if (true !== $userState) {
    error2(-4067,2);
}
$type = explode('/', $_FILES['file']['type']);
$e = $_FILES['file']['error'];
$defaultSize = $_FILES['file']['size'];
if ((int)$defaultSize > (int)(1024 * 1024 * MAX_UPLOAD_SIZE)) {
    error2(-4015, 2);
}
switch ($e) {
    case 0 :
        break;
    case 1 :
        error2(-4015,2);
        break;
    case 2 :
        error2(-4033,2);
        break;
    case 3 :
        error2(-4016,2);
        break;
    case 4 :
        error2(-4017,2);
        break;
    default:
        error2(-4018,2);
}
write_log('uid='.$uid.'－－－error=='.$e.'==size='.$defaultSize.'---type--'.json_encode($type),'imglog');
if (!in_array($type[1], array('jpg', 'jpeg', 'png', 'gif'))) {
    error2(-4019,2);
}
if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
    error2(-4020,2);
}

if ($ptype == 0) {
    $uploadPath = $pathConfig[$ptype] . '/' . date('Y', time()) . '/' . date('m', time()) . '/';
}
$upload = new UpLoad($conf['img-dir'] . '/' .$uploadPath);
$dir = $upload->exec($_FILES['file']); //上传
if (!empty($dir[0])) {
    if($ptype==0){
        $redis=new RedisHelp();
        $picturekey=$uid.':REPORT_PICTURE';
        $redis->set($picturekey,$uploadPath.$dir[0]);
    }
    succ(array('picture' =>$uploadPath.$dir[0]));
} else {
    error2(-5017);
}

