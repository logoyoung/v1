<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/4/23
 * Time: 下午4:52
 */
include '../../../include/init.php';
include INCLUDE_DIR.'Anchor.class.php';

$db = new DBHelperi_huanpeng();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? (int)$_POST['encpass'] : "";

if(!$uid || !$enc)
	error(-4013);

$anchor = new AnchorHelp($uid, $db);
if($loginStatus = $anchor->checkStateError($enc))
	error($loginStatus);

//检查主播状态
//检查认证状态

$tmp = $anchor->getWithdrawRecord();

foreach($tmp as $key => $val){
	$tmp[$key]['ctime'] = date('Y-m-d', strtotime($val['ctime']));
}
$tmp = toString($tmp);

exit(jsone(array('recordList'=>$tmp)));