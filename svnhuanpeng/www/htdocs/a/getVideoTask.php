<?php
include '../init.php';
/**
 * 获取任务
 * 
 *   */

/**
 * 说明：
 * 主要用于扫描录像生成任务队列表，
 * 获取任务并更改状态（上锁）
 * 返回
 * 成功：直播ID
 * 失败：false
 *
 * @param object $db
 * @param number $srcStatus//初始状态
 * @param number $dstStatus//目标状态
 * @param number $start//扫描起始行
 * 根据日志或者给定不同进程不同的起始位置以缩小扫描的范围
 * @return boolean|number
 */
function lockLiveByStatus($db, $dstStatus, $srcStatus, $start = NULL)
{
    if (! $start)
        $sql = "SELECT `liveid` FROM `videosave_queue` WHERE `status`={$srcStatus} LIMIT 1";
    else
        $sql = "SELECT `liveid` FROM `videosave_queue` WHERE `id`>{$start} `status`={$srcStatus}  LIMIT 1";
    $res = $db->query($sql);
    if (! $res)
        return 0;
    $row = $res->fetch_row();
    $liveid = $row[0] ? $row[0] : 0;
    if (! $liveid)
        return 0;
    $sql = "UPDATE `videosave_queue` SET `status`={$dstStatus} WHERE `liveid`={$liveid} AND `status`={$srcStatus}";
    $res = $db->query($sql);
    // 检查 当被其它进程上锁返回空行数
    if (! $db->affectedRows)
        return 0;
    // 同步live表
    updateLiveStatus($db, $liveid, 'live', $dstStatus, $srcStatus);
    return $liveid;
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
//mylog('--------------',LOGFN_VIDEO_SAVE_ERR);
$start = isset($_GET['start'])?(int)$_GET['start']:0;
$db = new DBHelperi_huanpeng();
$liveid = lockLiveByStatus($db, LIVE_SAVING, LIVE_STOP,$start);
echo $liveid;
exit;
