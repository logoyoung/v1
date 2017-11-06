<?php

/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/1/12
 * Time: 上午11:03
 */
/**
 * 获取我的认证详情
 */
include '../../../../include/init.php';
include INCLUDE_DIR . "Anchor.class.php";

use service\user\UserAuthService;

$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';

if (!$uid || !$enc) {
    error2(-4013);
}

// if(!$uid || !$enc) {
//     error2(-4013);
// }
$auth = new UserAuthService();
$auth->setUid($uid);
$auth->setEnc($enc);
//校验encpass、用户 登陆状态
if($auth->checkLoginStatus() !== true)
{
    //获取校验结果
    $result    = $auth->getResult();
    //错误码
    $errorCode = $result['error_code'];
    //错误消息
    $errorMsg  = $result['error_msg'];
    //假如是封禁的，可以获取禁时间
    $etime     = isset($result['login_disable_etime']) ? $result['login_disable_etime'] : 0;
    write_log("notice|uid:{$uid};error_code:{$errorCode};error_msg:{$errorMsg};解禁时间:{$etime}|api:".__FILE__,'auth_access');
    error2(-4067,2);
}

$r = get_userCertifyStatus($uid, $db);

rpc_ajax_filter($r['phone']);
rpc_ajax_filter($r['ident']);
rpc_ajax_filter($r['identname']);

function rpc_ajax_filter(&$string) {
    $len = ceil(mb_strlen($string) / 2);
    $pos = ceil(mb_strlen($string) / 4);
    $rep = str_pad('', $len, "*");
    $newStr = mb_substr($string, 0, $pos) . $rep . mb_substr($string, $len + $pos);
    $string = $newStr;
}

//包含未实名认证的主播
$r['isAnchor'] = $auth->checkIsAnchor() ? 1 : 0;
// 1显示普通认证渠道，2.显示芝麻认证渠道, 3.显示所有认证渠道, 0关必所有
$r['display_cert_channel'] = \service\user\UserCertCreateService::getDisplayCertChannel();
render_json($r);