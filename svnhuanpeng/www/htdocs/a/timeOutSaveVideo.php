<?php
/**
 * 直播超时录像生成调用
 *   */
include '../init.php';
include (INCLUDE_DIR . 'Live.class.php');

$liveid = isset($_GET['liveid'])?(int)$_GET['liveid']:0;
if(!$liveid){
    echo "failed";
    exit;
}
$live = new LiveHelp($liveid);
$r = $live->addLive2VideoRecord(VIDEO_SAVETYPE_TIMEOUT);
echo "success";
exit;
