<?php
include_once '../../../../include/init.php';
include INCLUDE_DIR."User.class.php";
use service\event\EventManager;

$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

header("Content-type:text/html;charset=utf-8");
session_start();

$certify_success = true;

if(!(int)$_GET['uid']){
    include WEBSITE_TPL . 'pleaseLogin.php';
    exit();
}

$uid = (int)$_GET['uid'];
$encpass = userHelp::getUserEncpass($uid);
if(!$encpass)
    include WEBSITE_TPL . 'pleaseLogin.php';

//检测是否登录 如果未登录先进行登录
//if (!$_COOKIE['_uid'] || !$_COOKIE['_enc'] || true !== checkUserState($_COOKIE['_uid'], $_COOKIE['_enc'], $db)) {
//    include WEBSITE_TPL . 'pleaseLogin.php';
//}

//检查是否已经验证
$sql = "select mail, mailstatus from userstatic where uid=$uid";
$res = $db->query($sql);
$row = $res->fetch_assoc();

$mail = $row['mail'];
$mailStatus = $row['mailstatus'];

if($mailStatus == EMAIL_PASS){
    $targetUrl = WEB_PERSONAL_URL;
    echo "<meta http-equiv='refresh' content='0;url={$targetUrl}'>";
    exit;
}

//redis 键名
$redis = new redishelp();

//$redis_sendCount = "certEmailSendCount:$uid";
//$redis_sendTime = "certEmailSendTime:$uid";
//$redis_certTime = "email_cert_time:$uid";
//
//$email_cert_time = (int)$redis->get($redis_certTime);
//
//print_r($email_cert_time);

//检测传入参数是否正确，如果不正确，进入错误提醒页面
$appKey = isset($_GET['appkey']) ? trim(urldecode($_GET['appkey'])) : '';
$eMail = isset($_GET['email']) ? trim(urldecode($_GET['email'])) : '';


if (!$appKey || !$eMail) {
    $certify_success = -1;

} else {
    $appKey_array = explode('-', $appKey);

    $time = $appKey_array[1];
    $appKey = $appKey_array[0];


    $key = md5($uid . CERT_EMAIL_KEY . $mail . $time);

    if (!$time || $time < time()) {// || $time != $email_cert_time
        $certify_success = -2;

    } elseif (!$mail || $mailStatus != EMAIL_UNPASS || $mail != $eMail) {
        $certify_success = -3;

    } elseif ($appKey != $key) {
        $certify_success = -4;

    }
}

if ($certify_success === true) {
    setUserLoginCookie($uid,$encpass);
    //验证成功，显示验证成功页面，5秒后跳转到个人中心
    $sql = "update userstatic set mailstatus = ".EMAIL_PASS." where uid=$uid";
    if ($db->query($sql)) {
        //清除redis 的值
//        $redis->del($redis_sendCount);
//        $redis->del($redis_sendTime);
//        $redis->del($redis_certTime);
        synchroTask($uid, 12, 0, 100, $db);//同步任务

        $event = new EventManager();
        $event->trigger(EventManager::ACTION_USER_INFO_UPDATE,['uid' => $uid]);
        $event = null;

        if(isMobile()){
            echo "<meta http-equiv='refresh' content='0;url=pass.php'>";
        }else{
            echo "认证成功";
            $targetUrl = WEB_PERSONAL_URL;
            echo "<meta http-equiv='refresh' content='3;url={$targetUrl}'>";
        }
    } else {
        echo "数据库更新失败";
        $targetUrl = WEB_PERSONAL_URL;
        echo "<meta http-equiv='refresh' content='3;url={$targetUrl}'>";
    }
} else {

    print_r($certify_success);
    echo "<br/>";
    print_r($_COOKIE);
    echo "<br/>";
    print_r($email_cert_time);
    echo "appKey=".$appKey;
    echo "<br/>";
    echo "key=" .$key;
    exit;

    echo "邮箱验证码过期，请重新认证";
    $targetUrl = WEB_PERSONAL_URL;
    echo "<meta http-equiv='refresh' content='3;url={$targetUrl}'>";

}

?>

