<?php
include '../init.php';
//mylog('--------------',LOGFN_VIDEO_SAVE_ERR);
/**
 * 说明:
 * 目前没有手机客户端没有及时消息推送
 * 采用站内信的消息通知形式
 * 
 * @param object $db            
 * @param number $uid            
 * @param string $msg            
 */
function msgCallBack($db, $uid, $title, $content)
{
    // TODO
    // 先调用站内信
    return sendMessages($uid, $title, $content, 0, $db);
    // 及时推送
    // sendRealTimeMessage($uid,$msg);
}

function getUserByLive($db, $liveid)
{
    $sql = "SELECT `uid` FROM `live` WHERE `liveid`={$liveid}";
    $res = $db->query($sql);
    if (! $res)
        return false;
    $row = $res->fetch_row();
    return $row[0] ? $row[0] : false;
}

/**
 * 说明：
 * 更改直播状态
 *
 * @param object $db            
 * @param number $liveid//直播ID            
 * @param String $table//表名            
 * @param number $status//目标状态            
 * @param number $status2//初始状态（可选）            
 * @return boolean
 */
function updateLiveStatus($db, $liveid, $table, $status, $status2 = NULL)
{
    if (! $status2)
        $sql = "UPDATE {$table} SET `status`={$status} WHERE `liveid`={$liveid}";
    else
        $sql = "UPDATE {$table} SET `status`={$status} WHERE `liveid`={$liveid} AND `status`={$status2}";
    $res = $db->query($sql);
    if (! $db->affectedRows)
        return false;
    return true;
}
$db = new DBHelperi_huanpeng();
$liveid = isset($_GET['liveid']) ? (int) $_GET['liveid'] : 0;
//$title = isset($_GET['title']) ? $_GET['title'] : '';
//$content = isset($_GET['content']) ? $_GET['content'] : '';
$vfile = isset($_GET['vfile'])?$_GET['vfile']:'';
$poster = isset($_GET['poster'])?$_GET['poster']:'';
$length = isset($_GET['length'])?$_GET['length']:0;
//mylog('--------------'.$vfile,LOGFN_VIDEO_SAVE_ERR);
$uid = getUserByLive($db, $liveid);
if (! $liveid || ! $uid ) {
    echo 0;
    exit;
}

// 同步live表
$rl = updateLiveStatus($db, $liveid,'live', LIVE_VIDEO, LIVE_SAVING);
$rv = updateLiveStatus($db, $liveid, 'videosave_queue', LIVE_VIDEO, LIVE_SAVING);
$rm = msgCallBack($db, $uid, '录像保存成功', "您的编号为{$liveid}的录像已经成功保存");
//var_dump($rl);
$sign = 0;
$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$videourl = "http://".$conf['domain'].'/'."main/a/videoConv.php?liveid={$liveid}&vfile={$vfile}&poster={$poster}&length={$length}&tm=".time()."&sign=$sign";
//var_dump($videourl);var_dump($rl);
var_dump($rl);
var_dump($rv);
var_dump($rm);
if ($rl && $rv && $rm)
    echo file_get_contents($videourl);
else
    echo 0;
exit;