<?php
ini_set('display_errors',1);            //错误信息
ini_set('display_startup_errors',1);    //php启动错误信息
include __DIR__."/../../include/init.php";
use service\push\SystemPush;

//---------------Socket Send 开播提醒--------------------------------------
//测试 URL  http://testapi.huanpeng.com/yalong/testSocketMsg.php?luid=2305&uid=2305
use lib\MsgPackage;
use lib\SocketSend;

if( isset($_GET['ss']) && $_GET['ss'] ==1 )
{
    $socket = new SocketSend();
    $msgpack= new MsgPackage();
    $db = new DBHelperi_huanpeng();
    
    $luid  = intval($_GET['luid']);
    $uid   = intval($_GET['uid']);
    $anchorNick='骨粉彪影';  
    $anchorPic='http://fvod.huanpeng.com/11935.jpg';
    $content = MsgPackage::getLiveStartNoticeMsgSocketPackage($luid, $uid, $anchorNick, $anchorPic);
    SocketSend::sendMsg( $content ,$db);
}else 
{
    $uidList = [2295,39925,2305];
    $title   = '系统消息下发测试';
    $msg     = '系统消息测试...';
    $action  = 'site-msg';
    $custom  = [];
    
    $systemObj = new SystemPush(); 
    $systemObj->send($uidList, $title, $msg, $action, $custom);
}