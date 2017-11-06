<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/10/24
 * Time: 下午12:46
 */

include '../../init.php';
require_once INCLUDE_DIR . 'class.geetestlib.php';
$GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
session_start();
$status = $GtSdk->pre_process();
$_SESSION['gtserver'] = $status;
echo $GtSdk->response_str;
?>