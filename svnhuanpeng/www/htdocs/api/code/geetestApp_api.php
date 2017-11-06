<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/7
 * Time: 上午10:39
 */
include '../../../include/init.php';
require_once INCLUDE_DIR.'class.geetestlib.php';

$GtSdk = new GeetestLib(CAPTCHA_APP_ID, PRIVATE_APP_KEY);

$uniqid = uniqid('_geetest_');

$geetestClientKey = '_geetest_client';

$status = $GtSdk->pre_process();

$redis = new RedisHelp();

$redis->set( $uniqid, $status, 600 );

hpsetCookie( $geetestClientKey, $uniqid );

//$_SESSION['gtserver'] = $status;
echo $GtSdk->response_str;
?>
