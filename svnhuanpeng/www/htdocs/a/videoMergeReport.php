<?php
include '../init.php';

$errMsg  = isset($_GET['errmsg'])?$_GET['errmsg']:'';
$errCode = isset($_GET['errcode'])?(int)$_GET['errcode']:0;
$taskId  = isset($_GET['taskid'])?(int)$_GET['taskid']:0;

if( !$errCode||!$errMsg||!$taskId ){
    echo 0;
    exit;
}
$db = new DBHelperi_huanpeng();
$errMsg = $db->realEscapeString($errMsg);
$sql = "INSERT INTO `admin_videomerge_failed`(`liveid`,`type`) VALUES($taskId,$errCode)";
$db->query($sql);
$sqllog = '';
if(!$db->affectedRows)
    $sqllog = 'insert into database table failed';
$log = "the task $taskId merge failed,and the errcode $errCode means that $errMsg  $sqllog";
mylog($log,LOGFN_VIDEO_MERGE_ERR);
echo 1;
exit;

