<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/7
 * Time: 上午10:39
 */
include '../../init.php';
require_once INCLUDE_DIR . 'class.geetestlib.php';
$GtSdk = new GeetestLib(CAPTCHA_APP_ID, PRIVATE_APP_KEY);
session_start();
$status = $GtSdk->pre_process();
$_SESSION['gtserver'] = $status;
echo $GtSdk->response_str;
?>
