<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/18
 * Time: 17:47
 */

include __DIR__."/../../../include/init.php";
include INCLUDE_DIR . '/loginSDK/ThreePartyLogin.php';


$client = 'web';
$channel = 'wechat';


$code = $_POST['code'] ?? '';

if( !$code )
{
    render_error_json("code值无效",-4031);
}

$data = [];
$data['code'] = $code;

$thrid = new \ThreePartyLogin($channel, $client, $data);


$err = $thrid->getError();


if($err['error_no'])
{
    render_error_json($err['error_msg'], $err['error_no']);
}
else
{
    $accessToken = $thrid->getParam("accessToken");
	$openid = $thrid->getParam("openid");

	if(!$accessToken || !$openid)
	{
		render_error_json("获取失败",-4033);
	}
	else
	{
		render_json([
			'access_token' => $accessToken,
			'opendid' => $openid
		]);
	}
}

