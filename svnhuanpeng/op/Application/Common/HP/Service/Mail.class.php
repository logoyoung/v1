<?php

namespace HP\Service;

class Mail {
	/**
	 *
	 */
	const APPID = '102';
	/**
	 *
	 */
	const KEY = 'ekxklhuangTSDpengfkjekldc';

	/**
	 *
	 */
	const DEV_SEND_URL = 'http://dev.liveuser.6.cn/api/pubSendEmailApi.php?';
	/**
	 *
	 */
	const PRO_SEND_URL = "http://liveuser/api/pubSendEmailApi.php?";


	/**
	 * @param array $content
	 *
	 * @return string
	 */
	public function sendMsg( array $content)
	{

	    $content['content'] = $ccc;
		$content['type'] = $content['type']?$content['type']:"registemail_102";
		$content['appid'] = self::APPID;
		$content['sign'] = self::_createSign($content);

		$str = http_build_query($content);
		$env = strtoupper($GLOBALS['env']);

		if($env == "PRO")
		{
			$sendURL = self::PRO_SEND_URL.$str;
		}
		else
		{
			$sendURL = self::DEV_SEND_URL.$str;
		}
		//$ret = file_get_contents($sendURL);
		echo $sendURL;exit;
		return $ret;
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	public function _createSign( array $data )
	{
		ksort( $data );

		return md5( json_encode( $data ) . self::KEY );
	}

}
