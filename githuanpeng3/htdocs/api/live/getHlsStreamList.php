<?php

include '../../../include/init.php';
use service\live\StreamDataService;
use service\user\UserAuthService;
/**
 * h5 获取直播流接口
 * @author guanlong
 */
$luid = isset($_POST['luid']) ? (int) $_POST['luid'] : 0;

if(!$luid)
{
    error2(-4013);
}

$auth = new UserAuthService;
$auth->setUid($luid);
if (!$auth->checkAnchorCertStatus())
{
    $log  = "error|获取直播流信息，非法主播; luid:{$luid}; api: ".__FILE__."; line:". __LINE__ ;
    write_log($log, 'auth_access');
    render_json(['orientation' => '', 'liveID' => '', 'streamList'=> [], 'stream' => '', 'isLiving'=> 0,]);
}

$multiStream = StreamDataService::getMultiStreamByAnchorUid($luid);
$list        = StreamDataService::getH5StreamByMultiStream($multiStream);

render_json($list);