<?php
/**
 * 更新心跳时间并解除过期的绑定
 * 可以解除管理员对审核内容的绑定
 * 缺陷是不能马上解除，需要刷新页面
 * User: shijiantao
 * Date: 17/5/12
 * Time: 上午11:55
 */
//ini_set('display_errors', 'On');
//error_reporting(E_ALL);
include '../includeAdmin/init.php';

$db = new DBHelperi_admin();

$uid = isset($_POST['uid']) ? (int) $_POST['uid'] : '';
$heartBeatType = isset($_POST['heartBeatType']) ? $_POST['heartBeatType'] : 0;
$dbTables = array(
	'realName' => 'admin_certRealName',
	//'liveTitle' => 'admin_wait_live_title', 
	'video' => 'admin_wait_pass_video', 
	//'userNick' => 'admin_wait_user_nick', 
	//'userPic' => 'admin_wait_user_pic', 
	//'videoComment' => 'admin_wait_video_comment'
);
if(!$uid || !isset($dbTables[$heartBeatType])) {
	error(-1007);
}

$date = date('Y-m-d H:i:s');

if($heartBeatType == 'realName') {  //数据库中，存放管理员id的字段名字不同
	$adminid = 'uid';
} else {
	$adminid = 'adminid';
}

//更新当前用户的心跳信息
$where = '`status`=' . 1 . ' and `' . $adminid . '`=' . $uid;
$data = array(
	'heartTime' => $date,
);
$db->where($where)->update($dbTables[$heartBeatType], $data);


//删除无心跳用户的绑定信息----5分钟未更新
$where = '`status`=' . 1 . ' and `heartTime`<"' . date('Y-m-d H:i:s', strtotime($date) - 300) . '"';
$data = array(
	'status' => 0,
	$adminid => 0,
);
$db->where($where)->update($dbTables[$heartBeatType], $data);