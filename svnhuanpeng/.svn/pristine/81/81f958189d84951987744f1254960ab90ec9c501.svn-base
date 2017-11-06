<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/10/24
 * Time: 下午12:46
 */

include '../../../include/init.php';
require_once INCLUDE_DIR.'class.geetestlib.php';
$GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
//session_start();

$uniqid = uniqid('_geetest_');

$geetestClientKey = '_geetest_client';

$status = $GtSdk->pre_process();

$redis = new RedisHelp();

$redis->set( $uniqid, $status, 600 );

write_log("geetest_api::".json_encode(['cookieKey'=>$geetestClientKey,'redisKey'=>$uniqid, 'status' => $status,'domain'=>$GLOBALS['env-def'][$GLOBALS['env']]['domain']]), "geetest.log");

hpsetCookie( $geetestClientKey, $uniqid );

//$_SESSION['gtserver'] = $status;
echo $GtSdk->response_str;
?>