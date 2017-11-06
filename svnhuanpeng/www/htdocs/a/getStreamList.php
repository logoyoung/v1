<?php

include '../init.php';
$db = new DBHelperi_huanpeng();

/**
 * 获取直播流名称和角度
 * @param type $luid
 * @param type $liveId
 * @param type $db
 * @return type
 */
function getStreamAndOrienta($luid, $db) {
    $res = $db->field('liveid,orientation,stream')->where('uid=' . $luid . '  AND  status=' . LIVE . '')->select('live');
    return $res;
}
/**
 * start
 */
$luid = isset($_POST['luid']) ? trim($_POST['luid']) : " ";
if (empty($luid)) {
    exit(-4013);
}
$luid = checkInt($luid);
getLiveServerList($streamServer, $notifyServer);
$row = getStreamAndOrienta($luid, $db);
if (empty($row)) {
    $orientation = '';
    $stream = '';
    $liveId='';
    $streamServer = array();
} else {
    $orientation = $row[0]['orientation'];
    $stream = $row[0]['stream'];
    $liveId=$row[0]['liveid'];
}
exit(jsone(array('streamList' => array($streamServer), 'orientation' => $orientation, 'stream' => $stream,'liveId'=>$liveId)));
