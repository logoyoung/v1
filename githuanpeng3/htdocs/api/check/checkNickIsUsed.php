<?php

include '../../../include/init.php';
use service\rule\TextService;

/**
 * 检测昵称是否被用过
 * date 2016-07-18 11:21
 * author yandong@6rooms.com
 */
$db = new DBHelperi_huanpeng();
$nick = isset($_POST['nick']) ? trim($_POST['nick']) : '';
if (empty($nick)) {
    error2(-4064,2);
}
$nick = filterData($nick);
if (mb_strlen($nick, 'utf8') > 12 || mb_strlen($nick, 'utf8') < 3) {
    error2(-4010,2);
}

$textService = new TextService();
$textService->setCaller('api:'.__FILE__.';line:'.__LINE__);
//关闭后如果接请求反垃圾接口网络服务异常都会返回true,默认通过
//$textService->setCallLevel(true);
$port = 0;
$textService->addText($nick,time(),TextService::CHANNEL_NICKNAME)->setIp(fetch_real_ip($port));
//反垃圾过滤
if(!$textService->checkStatus())
{
    succ(['isUsed' => "1",'desc' => '昵称包含敏感内容']);
}

$res = checkNickIsUsed($nick, $db);
if ($res) {
    succ(array('isUsed' => "1"));
} else {
    succ(array('isUsed' => "0"));
}
