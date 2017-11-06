<?php
include_once '../../../../include/init.php';
include_once INCLUDE_DIR.'User.class.php';

$db = new DBHelperi_huanpeng();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];


$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : 0;


if (!$uid || !$enc)
    error(-4009);
$code = checkUserState($uid, $enc, $db);
if ($code !== true) error($code);

//检查认证状态
$userHelp = new UserHelp($uid, $db);
$certStatus = $userHelp->getEmailCertifyInfo();
if($certStatus['status'] == EMAIL_PASS){
    error(-4047);
}


$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (!$email || !preg_match('/^\w+@(\w)+((\.\w+)+)$/', $email))
    error(-4042);

//$password = isset($_POST['password']) ? trim($_POST['password']) : '';

//if(!$password)
//	exit('-3');

//$sql = "select password from userstatic where uid = $uid";
//$res = $db->query($sql);
//$row = $res->fetch_assoc();
//
//if($row['password'] != md5password($password))
//	exit('-4');

//检查发送次数
$redis = new redishelp();
$redis_sendCount = "certEmailSendCount:$uid";
$redis_sendTime = "certEmailSendTime:$uid";
$redis_certTime = "email_cert_time:$uid";

if ($redis->get($redis_sendCount) >= 3) {
    if ((time() - $redis->get($redis_sendTime)) < EMAIL_CERT_OUTTIME) {
        error(-4043);
    } else {
        $redis->set($redis_sendCount, '0');
    }
}


//发送邮件
$email_cert_time = time() + EMAIL_CERT_OUTTIME;
$redis->set($redis_certTime, $email_cert_time);
//$_SESSION['email_cert_time'] = time() + EMAIL_CERT_OUTTIME;

$urlParam['appkey'] = md5($uid . CERT_EMAIL_KEY . $email . $email_cert_time) . '-' . $email_cert_time;
$urlParam['email'] = $email;

$temp = array();

foreach ($urlParam as $key => $val) {
    $p = $key . '=' . urlencode($val);
    array_push($temp, $p);
}

$url = join('&', $temp);

$content = 'http://'.$conf['domain'] . "/main/personal/mp/certify_email/certify.php?" . $url;
$nick = getUserInfo($uid, $db)[0]['nick'];
//邮件发送
$email_content = array(
    'username' => $nick,   //用户名
    'url' => $content,     //用户点击的url
    'expire' => '24小时',          //该点击url的有效时间
);
ksort($email_content);
$data = array(
    'appid' => "102",
    'type' => 'registemail_102',
    'email' => $email,
    'content' => json_encode($email_content)
);
$query = http_build_query($data);
$key = 'ekxklhuangTSDpengfkjekldc';
ksort($data);
$data = json_encode($data);
$sign = md5($data . $key);


//$email = $db->realEscapeString($email);
//$sql = "update userstatic set mail = '$email',mailstatus = 1 where uid=$uid";

//if ($db->query($sql)) {
//
//    $sendurl = "http://dev.liveuser.6.cn/api/pubSendEmailApi.php?sign=$sign&$query";
//    $res = file_get_contents($sendurl);
//    $r = json_decode($res, true);
//    if ($r['resuNo'] == 1) {
//
//    } else {
//        exit(json_encode(array('code' => $r['resuNo'], 'desc' => $r['resuMsg'])));
//    }
//} else {
//    exit(json_encode(array('isSuccess' => 0)));
//}


$sendurl = "http://dev.liveuser.6.cn/api/pubSendEmailApi.php?sign=$sign&$query";
$res = file_get_contents($sendurl);
$r = json_decode($res, true);
if ($r['resuNo'] == 1) {
    $email = $db->realEscapeString($email);
    $sql = "update userstatic set mail = '$email',mailstatus = ".EMAIL_UNPASS." where uid=$uid";
    if ($db->query($sql)) {
        $sendCount = (int)$redis->get($redis_sendCount) + 1;
        $redis->set($redis_sendCount, "$sendCount");
        $redis->set($redis_sendTime, toString(time()));
        exit(json_encode(array('isSuccess' => 1, 'url' => $content, 'sendmail' => $r)));
    } else {
        error(-5001);
    }
} else {
    error(-4044);
}





