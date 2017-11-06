<?php
namespace lib;
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/29
 * Time: 下午7:55
 */

/**
 * Class MailSend
 *
 * @package hp\lib
 */
class MailSend
{
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
		$content['appid'] = self::APPID;
		$content['sign'] = $this->_createSign($content);

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
		$this->_log($sendURL);
		$ret = file_get_contents($sendURL);

		return $ret;
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	private function _createSign( array $data )
	{
		ksort( $data );

		return md5( json_encode( $data ) . self::KEY );
	}

	private function _log($msg)
	{
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}

}