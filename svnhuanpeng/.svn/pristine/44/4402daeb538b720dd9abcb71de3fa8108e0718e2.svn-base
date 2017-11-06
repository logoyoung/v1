<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/4/27
 * Time: 14:38
 */
include( __DIR__ . '/../../include/init.php' );
//$queueFile = LOG_DIR.'queue.txt';
function saveTxt($msg,$file){
	$r = file_put_contents($file, $msg);
	return $r;
}
$mergeRecordFile = LOG_DIR.'mergeRecord.txt';
$db = new DBHelperi_huanpeng();
//$stopLives = $db->where("status=101 and ctime>DATE_SUB(CURDATE(),INTERVAL 1 DAY)")->select('live');
$mergeRecord = $db->where("status<3 and ctime>DATE_SUB(CURDATE(),INTERVAL 5 DAY)")->select('video_merge_record');

saveTxt(json_encode($mergeRecord),$mergeRecordFile);

