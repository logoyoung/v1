<?php
require __DIR__.'/../../include/init.php';

use service\rule\TextService;



//留言内容
function testComment()
{
    $textService = new TextService();
    $textService->setCaller('api:'.__FILE__.';line:'.__LINE__);
    //$comment 留言内容 $uid 留言用户
    $comment = '习大大';
    $uid     = 88888;
    $textService->addText($comment,$uid,TextService::CHANNEL_DYNAMIC_COMMENT)->setIp(fetch_real_ip($port));

    //反垃圾过滤
    if(!$textService->checkStatus())
    {
        //内容包含敏感内容
        //自己处理错误
        exit('内容包含敏感内容a');
    }

}


//描述
function testDesc()
{
    $textService = new TextService();
    $textService->setCaller('api:'.__FILE__.';line:'.__LINE__);
    //$desc 描述 $uid 用户
    $desc[0] = 'test不要过滤';
    $desc[1] = 'test不要过滤';
    $desc[2] = 'test不要过滤';
    $uid  = 88888;
    $port = 0;
    $textService->addText($desc[0], $uid, TextService::CHANNEL_THEME)->setIp(fetch_real_ip($port));
   // $textService->addText($desc[1], 9999, TextService::CHANNEL_THEME)->setIp(fetch_real_ip($port));
    // $textService->addText($desc[2], 7777, TextService::CHANNEL_THEME)->setIp(fetch_real_ip($port));
    // $textService->addText($desc[0], $uid, TextService::CHANNEL_THEME)->setIp(fetch_real_ip($port));
    // $textService->addText($desc[1], 9999, TextService::CHANNEL_THEME)->setIp(fetch_real_ip($port));
    // $textService->addText($desc[2], 7777, TextService::CHANNEL_THEME)->setIp(fetch_real_ip($port));
    // $textService->addText($desc[0], $uid, TextService::CHANNEL_THEME)->setIp(fetch_real_ip($port));
    // $textService->addText($desc[1], 9999, TextService::CHANNEL_GROUP_CHAT)->setIp(fetch_real_ip($port));
    // $textService->addText($desc[2], 7777, TextService::CHANNEL_TEAM_CHAT)->setIp(fetch_real_ip($port));
    $r = $textService->checkStatus();
    var_dump($r);
    // print_r($textService->getResult());
}

//testComment();
//testDesc();


//die;
$liveParams['title']     = '吉泽明步欢朋';
$liveParams['gamename']  = '王者荣耀熊猫';
$liveParams['deviceid']  = '6628638A-FF7E-4686-82AE-8580D5B965A71';
$liveParams['nick']      = '第一美女主播';
$uid = '52445';

//接入反垃圾
$textService = new TextService();
$textService->setCaller('api:'.__FILE__.';line:'.__LINE__);
$textService->setCallLevel(true);
$textService->addText($liveParams['title'],     $uid, TextService::CHANNEL_THEME)
            ->setDeviceId($liveParams['deviceid']);
            //->setIp('122.70.146.49');

$textService->addText($liveParams['gamename'],  $uid, TextService::CHANNEL_THEME)->setDeviceId($liveParams['deviceid']);
$textService->addText($liveParams['nick'],  $uid, TextService::CHANNEL_NICKNAME)->setDeviceId($liveParams['deviceid']);
$textStatus  = $textService->checkStatus();


var_dump($textStatus);
if(array_search(false, $textStatus, true) !== false )
{
     echo 'error';
    echo "\n";
} else {
    echo 'succ';
}
die;
$shumei = new TextService();

$arr    = [
    // '找小姐打炮',
    // '北京小姐多少钱一晚',
    // '有人约炮吗',
    // 'av片出售',
    // '666打得太好了',
    // '求带我，老司机',
    // 'sb',
    // '傻逼卢本伟，，，，',
    // '卢本伟牛逼卢本伟牛逼卢本伟牛逼卢本伟牛逼',
   // '带节奏的不是脑残？',
    // '还差点，已经坑了',
    // '毒纪的狗滚出去。',
    '毛泽东老',
    // '卢本伟牛逼卢本伟牛逼卢本伟牛逼卢本伟',
    // '卢本伟牛逼卢本',
    // '带节奏的不',
    // '带节奏的不是脑残？1',
    // '带节奏的不是脑残？2',
    // '带节奏的不是脑残？3',
    // '带节奏的不是脑残？4',
    // '带节奏的不是脑残？5',
    // '带节奏的不是脑残？6',
    // '带节奏的不是脑残？7',
    // '带节奏的不是脑残？8',
];

foreach ($arr as $v) {
    $shumei->addText($v,uniqid(),'MESSAGE');
}

var_dump($shumei->checkStatus());
