<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/7/12
 * Time: 上午11:55
 */
ini_set('display_errors', 'On');
error_reporting(E_ALL);
include '../../includeAdmin/init.php';
include '../../includeAdmin/Admin.class.php';
include INCLUDE_DIR."live/Review.class.php";

$db = new DBHelperi_admin();
//msgtype : 1:notice, 2:stoplive, 3:killuser
$request = array('liveid'=>'int', 'reason'=>'int', 'msgType'=>'int', 'uid'=> 'int', 'encpass' => 'str', 'anchorid' => 'int', 'type'=>'int');
$msgTypeGroup = array('notice' => 1, 'stop'=>2, 'kill'=>3);

foreach($request as $key => $val){
    if($val == 'int') {
        $$key = (int)$_POST[$key];
    } else {
        $$key = trim($_POST[$key]);
    }

    if(!$$key) { 
        //error(-1007);
    }
}

if (empty($uid) || empty($encpass) || empty($type)) {
    error(-1007);
}

$adminHelp = new AdminHelp($uid, $type);
$err = $adminHelp->loginError($encpass);
if ($err) {
    error($err);
}

if($anchorid && !$liveid) //快速处理，传了用户ID，没有传直播ID
{
    if(!anchorIsExists($anchorid, $db)) {
        error(-1001);
    }
    $liveid = getLiveIdByUid($anchorid, $db);
}

if(!in_array($msgType, $msgTypeGroup)) {
    error(-1007);
}


use hp\live\Review as Review;
$review = new Review($uid, $db);

$res = $review->insertRecord($uid, $anchorid, $type, $reason, $liveid);

if($msgType == 3 && $liveid == 0){ //对未在进行直播的主播进行封号
    $ret = $review->killNoLive($anchorid);
    if($ret) {
        succ();
    } else {
        error(-1011);
    }
} else {
    $method = array_search($msgType, $msgTypeGroup);
    $ret = $review->$method($liveid, $reason);
    $ret = json_decode($ret);
    if($ret->status == 1) {
        succ(); 
    } else {
        error(-1011);
    }
}
    
function getLiveIdByUid($anchorid, $db){
    $res=$db->field("liveid")->where("uid=$anchorid and status=".LIVE)->limit(1)->select("live");
    if(!empty($res)){
        return $res[0]['liveid'];
    }else{
        return 0;
    }
}

//判断主播是否存在
function anchorIsExists($anchorid, $db)
{
    $res = $db->field('uid')->where("uid=$anchorid")->select('anchor');
    if($res) {
        return true;
    }
    return false;
}
