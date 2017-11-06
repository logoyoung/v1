<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 16/11/24
 * Time: 下午9:13
 */

include '../../../include/init.php';
include INCLUDE_DIR."User.class.php";
include INCLUDE_DIR.'/loginSDK/ThreePartyLogin.php';

//$requestParam = array(
//	'uid'=>'int',
//	'encpass'=>'',
//	'channel'=>'string',
//	'accessToken'=>'string',
//	'code'=>'string',
//	'openid'=>'string',
//	'tm'=>'string',
//	'client'=>'', //ios,android
//	'sign'=>'string'
//);
$must = array(
	'channel'=>array(
		'type'=>'string',
		'must'=>true,
		'values'=>['weibo','wechat','qq']
	),
	'client' => array(
		'type' => 'string',
		'must'=>true,
		'values'=>['android','ios']
	),
	'uid'=>array(
		'type'=>'string',
		'must' => true,
	),
	'encpass'=>array(
		'type'=>'string',
		'must'=>true
	)
);


$wechatMust = array(
	'code'=>array('type'=>'string','must'=>true)
);

$weiboMust =$qqMust= array(
	'accessToken'=>array('type'=>'string','must'=>true),
	'openid'=>array('type'=>'string','must'=>true),
);

$data = $_POST;
$params = array();

if(checkParam($must, $data, $params))
	foreach ($params as $key =>$val) $$key = $val;
else
	error2(-4013);

$channelMust = $channel."Must";
if(checkParam($$channelMust, $data, $params))
	foreach ($params as $key=>$val) $$key = $val;
else
	error2(-4013);

//验证认证结果
if(!verifySign($_POST,SECRET_KEY)){
	error2(-4024);
}

if($channel == 'wechat'){
	$data['code'] = $code;
}else{
	$data['accessToken'] = $accessToken;
	$data['openid'] = $openid;
}

$db = new DBHelperi_huanpeng();
$threePart = new ThreePartyLogin($channel,$client,$data,$db);
if(!$threePart){
	error2(-4069,2);
}

$ret = $threePart->run('bind', $uid, $encpass);
if($ret === true){
    $nick=getThreeNick($uid,$channel,$db);
	succ(array('nick'=>$nick));
}else{
	error2($ret,2);
}