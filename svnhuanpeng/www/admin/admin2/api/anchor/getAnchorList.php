<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/6/28
 * Time: 下午2:13
 */

include '../../includeAdmin/init.php';
include INCLUDE_DIR . 'Admin.class.php';
include INCLUDE_DIR . 'Anchor/Review.class.php';

$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_admin();

$uid = isset($_POST['uid']) ? (int)$_POST['uid'] : 0;
$enc = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$type = isset($_POST['type']) ? (int)$_POST['type'] : '';

if(!$uid || !$enc || !$type)
    error(-1007);
if(in_array($uid,array(1,2,7,41))){
    succ(array());
}
$adminHelp = new AdminHelp($uid, $type, $db);

$err = $adminHelp->loginError($enc);
if($err) error($err);

$review = new ReviewUser($db);

$taskid = $review->getLockTask($uid);
if(!$taskid) {
    $taskid = $review->getNewTask();

    if($taskid){
        $review->setLock($taskid, $uid);
    }else{
        error(-1009);
    }
}

$info = $review->getRealNameInfo($taskid);

$anchorId = $info['uid'];
if($anchorId)
	$anchorInfo = getUserInfos($anchorId, $db);
else
	$anchorInfo = array();


function getUserCost($uid, $db){
    $sql = "select sum(purchase) as mycount from billdetail where customerid = $uid";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();

    return (int)$row['mycount'];
}

function getUserInfos($uid, $db){
    $sql = "select pic, nick, rtime, level from userstatic, useractive where userstatic.uid = $uid and userstatic.uid = useractive.uid";
    $res = $db->query($sql);
    $row = $res->fetch_assoc();
    return $row;
}

$ret = array(
    'nick' => $anchorInfo['nick'],
    'pic' => $anchorInfo['pic'] ? "http://".$conf['domain-img'].'/'.$anchorInfo['pic'] : DEFAULT_PIC,
    'rtime' => $anchorInfo['rtime'],
    'cost' => getUserCost($anchorId, $db),
    'level' => 'LV'.$anchorInfo['level'],
    'name' => $info['name'],
    'front' => "http://".$conf['domain-img'].$info['face'],
    'back' => "http://".$conf['domain-img'].$info['back'],
    'held' => "http://".$conf['domain-img'].$info['handheldPhoto'],
    'papersid' => $info['papersid'],
    'outTime' => $info['papersetime'],
    "id" => $info['id']
);

succ($ret);
