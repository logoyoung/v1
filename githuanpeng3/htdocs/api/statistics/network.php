<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/6/19
 * Time: 上午11:04
 */

include __DIR__."/../../../include/init.php";

$logDir = LOG_DIR."network/net-".date("Ymd").".log";

$api = "http://ip.taobao.com/service/getIpInfo.php?ip=";

if($_POST['info'])
{
	$content = $_POST['info'];
	$content = hp_base64Decode($content);

	$content = json_decode($content, true);

	$port = 0;
	$ip = fetch_real_ip($port);

	$content['device']['publicIP'] = $ip;

	if(!$content['device']['carrieroperator'])
	{
		$res = file_get_contents($api."$ip");

		$res = json_decode($res, true);
		if($res && $res['code'] == 0)
		{
			$content['device']['carrieroperator'] = $res['data']['isp'];
		}
	}

	// $content['timestamp'] = time();
	$content['ctime'] = date("Y-m-d H:i:s");
	$content = json_encode($content);

	file_put_contents($logDir,$content."\n",FILE_APPEND);
}

succ();