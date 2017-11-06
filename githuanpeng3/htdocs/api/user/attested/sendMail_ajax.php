<?php
include '../../../../include/init.php';
//include_once INCLUDE_DIR.'User.class.php';
//include_once INCLUDE_DIR.'mailSend.class.php';
use lib\User;
use lib\MailBiz;
use lib\MsgPackage;


/**
 * 邮箱认证接口（发送邮件）
 */
$db    = new DBHelperi_huanpeng();
$redis = new RedisHelp();
$conf  = $GLOBALS['env-def'][$GLOBALS['env']];


$uid = isset( $_POST['uid'] ) ? (int)$_POST['uid'] : 0;
$enc = isset( $_POST['encpass'] ) ? trim( $_POST['encpass'] ) : 0;


if ( !$uid || !$enc )
{
	error2( -4013 );
}

$userHelp = new User( $uid, $db, $redis );
$code     = $userHelp->checkStateError( $enc );

if ( true !== $code )
{
	error2( -4067, 2 );
}

//检测邮箱认证状态
$status = $userHelp->getCertifyInfo();
if ( $status['emailstatus'] == EMAIL_PASS )
{
	error2( -4047, 2 );
}


$email = isset( $_POST['email'] ) ? trim( $_POST['email'] ) : '';

if ( !$email || !preg_match( '/^\w+@(\w)+((\.\w+)+)$/', $email ) )
{
	error2( -4042, 2 );
}

$nick = $userHelp->getUserInfo()['nick'];


$mailBiz = new MailBiz( $db, $redis );

$sendType = MsgPackage::MAIL_TYPE_BINDMAIL;

if ( !$mailBiz->canSendMsg( $sendType, $email ) )
{
	error2( -4043, 2 );
}

$url = '';

$callBackUrl = WEB_PERSONAL_URL . "mp/certify_email/certify.php";
$content     = MsgPackage::getCertifyEmailMsgEmailPackage( $uid, $nick, $callBackUrl, $email );

if ( !$mailBiz->sendMsg( $uid, $content, $url ) )
{
	error2( -4044, 2 );
}
else
{
	$userHelp->updateUserMailStatus($email,User::EMAIL_UNPASS);
	succ( array( 'url' => $url ) );
}


//$codeid = mailSend::createCodeId( mailSend::T_CERT_EMAIL, $email, $uid, $db );
//
//if ( !$codeid )
//{
//	error2( -4044, 2 );
//}
//else
//{
////	succ(['url'=>$u]);
//}
//
//$url     = mailSend::getEmailContentUrl( $uid, $email );
//$content = mailSend::getEmailContent( $nick, $url );
//
//if ( !mailSend::sendMsg( $content, $db, $redis ) )
//{
//	error2( -4044, 2 );
//}
//else
//{
//	$email = $db->realEscapeString( $email );
//	$sql   = "update userstatic set mail = '$email',mailstatus = " . EMAIL_UNPASS . " where uid=$uid";
//	if ( $db->query( $sql ) )
//	{
//		succ( array( 'url' => $url ) );
//	}
//	else
//	{
//		error2( -5001, 2 );
//	}
//}

//检查发送次数
//$redis = new redishelp();
//$redis_sendCount = "certEmailSendCount:$uid";
//$redis_sendTime = "certEmailSendTime:$uid";
//$redis_certTime = "email_cert_time:$uid";
//
//if ($redis->get($redis_sendCount) >= 3) {
//    if ((time() - $redis->get($redis_sendTime)) < EMAIL_CERT_OUTTIME) {
//        error2(-4043,2);
//    } else {
//        $redis->set($redis_sendCount, '0');
//    }
//}
//
//
////发送邮件
//$email_cert_time = time() + EMAIL_CERT_OUTTIME;
//$redis->set($redis_certTime, $email_cert_time);
////$_SESSION['email_cert_time'] = time() + EMAIL_CERT_OUTTIME;
//
//$urlParam['appkey'] = md5($uid . CERT_EMAIL_KEY . $email . $email_cert_time) . '-' . $email_cert_time;
//$urlParam['email'] = $email;
//$urlParam['uid'] = $uid;
//$url = http_build_query($urlParam);
//
//$content = DOMAIN_PROTOCOL.$conf['domain'] . "/main/personal/mp/certify_email/certify.php?" . $url;
//$nick = getUserInfo($uid, $db)[0]['nick'];
////邮件发送
//$email_content = array(
//    'username' => $nick,   //用户名
//    'url' => $content,     //用户点击的url
//    'expire' => '24小时',          //该点击url的有效时间
//);
//ksort($email_content);
//$data = array(
//    'appid' => "102",
//    'type' => 'registemail_102',
//    'email' => $email,
//    'content' => json_encode($email_content)
//);
//$query = http_build_query($data);
//$key = 'ekxklhuangTSDpengfkjekldc';
//ksort($data);
//$data = json_encode($data);
//$sign = md5($data . $key);
//
//
////$email = $db->realEscapeString($email);
////$sql = "update userstatic set mail = '$email',mailstatus = 1 where uid=$uid";
//
////if ($db->query($sql)) {
////
////    $sendurl = "http://dev.liveuser.6.cn/api/pubSendEmailApi.php?sign=$sign&$query";
////    $res = file_get_contents($sendurl);
////    $r = json_decode($res, true);
////    if ($r['resuNo'] == 1) {
////
////    } else {
////        exit(json_encode(array('code' => $r['resuNo'], 'desc' => $r['resuMsg'])));
////    }
////} else {
////    exit(json_encode(array('isSuccess' => 0)));
////}
//
//
//$sendurl = "http://dev.liveuser.6.cn/api/pubSendEmailApi.php?sign=$sign&$query";
//$res = file_get_contents($sendurl);
//$r = json_decode($res, true);
//if ($r['resuNo'] == 1) {
//    $email = $db->realEscapeString($email);
//    $sql = "update userstatic set mail = '$email',mailstatus = ".EMAIL_UNPASS." where uid=$uid";
//    if ($db->query($sql)) {
//        $sendCount = (int)$redis->get($redis_sendCount) + 1;
//        $redis->set($redis_sendCount, "$sendCount");
//        $redis->set($redis_sendTime, toString(time()));
//        succ(array('url' => $content, 'sendmail' => $r));
//    } else {
//        error2(-5001,2);
//    }
//} else {
//    error2(-4044,2);
//}
//
//
//
//
//
