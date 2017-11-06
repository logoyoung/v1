<?php
include '../include/init.php';
/**
 * 说明:
 * 目前没有手机客户端没有及时消息推送
 * 采用站内信的消息通知形式
 * @param object $db
 * @param number $uid
 * @param string $msg  */
function msgCallBack($db, $uid, $title, $content)
{
    // TODO
    //先调用站内信
    return sendMessages($uid, $title, $content, 0, $db);
    //及时推送
    //sendRealTimeMessage($uid,$msg);
}
$uid = isset($_GET['uid'])?$_GET['uid']:0;
$title = isset($_GET['title'])?$_GET['title']:'';
$content = isset($_GET['content'])?$_GET['content']:'';
if(!$uid||!$title||!$content){
    echo 0;
    exit;
}
$db = new DBHelperi_huanpeng();
$r = msgCallBack($db, $uid, $title, $content);
echo $r?1:0;
exit;