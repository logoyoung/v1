<?php

include '../../../include/init.php';
/*
 * 解绑第三方账户
 * date 2016-12-06 19:30
 * author yandong@6rooms.com
 * version 0.0
 */

use lib\User;

$conf = $GLOBALS['env-def'][$GLOBALS['env']];
$db = new DBHelperi_huanpeng();

/**检测已解绑次数
 * @param $uid  用户id
 * @param $channel  渠道
 * @param $db
 * @return bool|int
 */

function getThreeSideInfo($uid,$channel, DBHelperi_huanpeng $db)
{
	$sql = "select id,openid, unionid from three_side_user WHERE uid=$uid and channel='$channel'";
	$res = $db->query($sql);
	if(!$res)
	{
		return false;
	}

	$row = $res->fetch_assoc();
	if(!$row['id'])
	{
		return false;
	}

	if($channel == 'weibo')
	{
		return $row['openid'];
	}
	else
	{
		return $row['unionid'];
	}

}

function checkIsCancel($uid, $channel, $db)
{
    if (empty($uid) || empty($channel)) {
        return false;
    }

    $mark = getThreeSideInfo($uid,$channel,$db);

    if(!$mark)
	{
		return false;
	}

    if($channel == 'weibo')
	{
		$sql = "select count(*) as total from three_side_user where openid = '$mark' AND channel='$channel'";
	}
	else
	{
		//todo 该查询没有索引支持，以后需要增加unionID的索引
		$sql = "select count(*) as total from three_side_user WHERE channel = '$channel' AND unionid='$mark'";
	}

	$res = $db->query($sql);
    if(!$res)
	{
		return false;
	}

	$row = $res->fetch_assoc();

    return $row['total'];
}

/**更改绑定状态
 * @param $uid  用户id
 * @param $channel  渠道
 * @param $db
 * @return bool
 */
function unbindParty($uid, $channel, $db)
{
    if (empty($uid) || empty($channel)) {
        return false;
    }
    $res = $db->where("uid=$uid and channel='$channel' and status=1")->update('three_side_user', array('status' => 0));
    if (false !== $res) {
        return true;
    } else {
        return false;
    }
}

function checkValidBind($uid, $db)
{
    $phone = get_userPhoneCertifyStatus($uid, $db);
    if(isset($phone['phonestatus'])&&$phone['phonestatus'])
        return true;
    $res = $db->field('count(id) as count')->where("uid={$uid} and status=1")->select('three_side_user');
    if($res == false)
        return false;
    if(!isset($res[0]['count'])||!$res[0]['count'])
        return false;
    return true;
}

function updateLoginStatus($channel, $uid, $db)
{
    if(!$channel||!(int)$uid||!$db)
        return false;
    if(!checkValidBind($uid, $db))
        return false;
    if(!isset($_COOKIE['_loginway']) || $_COOKIE['_loginway']!=$channel )
        return false;
    hpdelCookie('_loginway');
    hpdelCookie('_uid');
    hpdelCookie('_enc');
}
/**
 * start
 */
$uid = isset($_POST['uid']) ? (int)($_POST['uid']) : '';
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
$channel = isset($_POST['channel']) ? trim($_POST['channel']) : '';
if (empty($uid) || empty($encpass) || empty($channel)) {
    error2(-4013);
}
$uid = checkInt($uid);
$encpass = checkStr($encpass);
$s = CheckUserIsLogIn($uid, $encpass, $db);
if (true !== $s) {
    error2(-4067, 2);
}
if(!in_array($channel,array('weibo','wechat','qq'))){
 error2(-4070,2);
}

$auth = new \service\user\UserAuthService();
$auth->setUid($uid);
$auth->setEnc($encpass);

if($auth->checkLoginStatus() !== true)
{
	error2(-1013);
}

$checkiscancel = checkIsCancel($uid, $channel, $db);

if ((int)$checkiscancel >= 3) {
    error2(-4073, 2);
}
$res = unbindParty($uid, $channel, $db);
if ($res) {
    //updateLoginStatus($channel);
    $validBind = checkValidBind($uid, $db)?1:0;
    succ(array('validBind'=>$validBind));
} else {
    error2(-5017);
}
