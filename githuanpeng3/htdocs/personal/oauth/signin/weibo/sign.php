<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/3/31
 * Time: 下午6:55
 */

session_start();
require '../../../../../include/init.php';
include INCLUDE_DIR . 'loginSDK/weibo/config.php';
include INCLUDE_DIR. 'loginSDK/weibo/saetv2.ex.class.php';

$oauth = new SaeTOAuthV2(WB_AKEY, WB_SKEY);
