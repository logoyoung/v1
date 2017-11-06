<?php

/**
 * 上传头像&&生成缩略图
 */
include '../init.php';
require(INCLUDE_DIR . 'upload.class.php');
require(INCLUDE_DIR . 'Img.class.php');
require_once INCLUDE_DIR . 'redis.class.php';
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$db = new DBHelperi_huanpeng();
$thumb_size = array(640); //压缩后图片尺寸数组
/**
 * 检测图片是否合法
 * @return boolean || array
 */
$header = json_encode($_SERVER); //var_dump(json_encode($GLOBALS['_POST'],JSON_UNESCAPED_UNICODE));
//mylog('user:'.$header,LOGFN_VIDEO_SAVE_ERR);
/**
 * 生成缩略图
 * @param string $dir 图片路径
 * @param array $conf 图片服务器相关路径
 * @param array $thumb_size //压缩后的尺寸
 * @param object $imgobj //实例化的对象
 * @return array
 */

function makeThumbPic($dir, $uid, $conf, $thumb_size)
{
    $cutstr = substr($dir[0], 0, 4);
    $t = new ImgHelp();
    $path = $conf['img-dir'] . '/userPic/' . $uid . '/' . $dir[0];
    for ($i = 0, $k = count($thumb_size); $i < $k; $i++) {
        $res = $t->setSrcImg($path);
        $t->setCutType(1); //这一句就OK
        $t->setDstImg($path);
        $res = $t->createImg($thumb_size[$i], $thumb_size[$i]);
    }
    return array('res' => true, 'pic' => $dir[0], 'showpic' => $dir[0], 'path' => '/userPic/' . $uid . '/' . $dir[0]);
}

/**
 * 头像入库
 * @param array $res
 * @param object $db
 * @return array
 */
function addPic($res, $uid, $db)
{
    if ($res['res'] == true) {
        $datas = array(
            'pic' => $res['path']
        );
        $uresult = $db->where("uid=$uid")->update('userstatic', $datas);
    }
    return $uresult ? $uresult : array();
}

/*
 * start
 */
$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : '';
$encpass = isset($_POST['encpass']) ? $_POST['encpass'] : '';


if (!$uid || !$encpass) {
    error(-4013);
}
//检查用户登陆状态
$userState = checkUserState($uid, $encpass, $db);
if (true !== $userState) {
    error($userState);
}
$type = explode('/', $_FILES['file']['type']);
$e = $_FILES['file']['error'];
switch ($e) {
    case 0 :
        break;
    case 1 :
        error(-4015);
        break;
    case 2 :
        error(-4033);
        break;
    case 3 :
        error(-4016);
        break;
    case 4 :
        error(-4017);
        break;
    default:
        error(-4018);
}
if (!in_array($type[1], array('jpg', 'jpeg', 'png', 'gif'))) {
    error(-4019);
}
if (!is_uploaded_file($_FILES['file']['tmp_name'])) {
    error(-4020);
}
$upload = new UpLoad($conf['img-dir'] . '/userPic/' . $uid . '/',array('dirwrite'=>false));

$dir = $upload->exec($_FILES['file']); //上传
$res = makeThumbPic($dir, $uid, $conf, $thumb_size); //缩放
if ($res['res'] == true) {
    $redisObj = new redishelp();
    $keys = "IsFirstUploadPic:" . $uid;
    $resul = $redisObj->get($keys);
    if (!$resul) {
        $redisObj->set($keys, 1); //设置标志
        synchroTask($uid, 6, 0, 100, $db); //同步任务
    }
    $checkHeadMode = checkMode(CHECK_HEAD, $db);//校验头像审核模式
    if ($checkHeadMode) {
        //先发后审
        addPic($res, $uid, $db); //入库
        $status = USER_PIC_AUTO_PASS;
    } else {
        $status = USER_PIC_WAIT;//先审后发
    }
//        exit(json_encode(array("userPic" => "http://" . $conf['domain-img'] . '/' . $res['showpic'])));
    admin_user_pic($uid, $res['path'], $db, $status);//头像审核
    exit(json_encode(array('userPic' => 'http://' . $conf['domain'] . "/main/a/server/getUserHead.php?" . http_build_query(array('time' => time(), 'uid' => $uid, 'enc' => $encpass)), 'picCheckStat' => 0)));
} else {
    error(-5017); //压缩时出错
}
  