<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/4/26
 * Time: 11:36
 */

namespace lib;


class ApplePush
{
	const PRO_SEND_URL ="http://applepie/push.php";

	const DEV_SEND_URL = "http://pncp.dev/push.php";

	const PRO_PUSH_PROD = 51;

	const DEV_PUSH_PROD = 50;

	private $_sendURL = '';

	public function __construct()
	{
		if( $GLOBALS['env'] == "PRO" )
		{
			$this->_sendURL = self::PRO_SEND_URL;
			$this->_prod    = self::PRO_PUSH_PROD;
		}
		else
		{
			$this->_sendURL = self::DEV_SEND_URL;
			$this->_prod    = self::DEV_PUSH_PROD;
		}
	}

	public function send( $content )
	{
		$content['prod'] = $this->_prod;
		return $this->_send( $content );
	}

	private function _checkData()
	{
		return true;
	}

	private function _send( $data )
	{
		$url = $this->_sendURL . "?" . http_build_query( $data );
		mylog($url);
		$get = "curl -Ss '$url'";

		$result = `$get`;

		return $result;
	}
}