<?php
namespace lib;
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/29
 * Time: 下午7:56
 */
/**
 * Class SmsSend
 *
 * @package hp\lib
 */
class SmsSend
{
	/**
	 *
	 */
	const APPID = 102;
	/**
	 *
	 */
	const KEY = 'ekxklhuangTSDpengfkjekldc';

	/**
	 *
	 */
	const DEV_SEND_URL          = 'http://dev.liveuser.6.cn/api/pubSendSmsCodeApi.php?';
	/**
	 *
	 */
	const PRO_SEND_URL          = 'http://liveuser/api/pubSendSmsCodeApi.php?';
	/**
	 *
	 */
	const DEV_SEND_CALLBACK_URL = 'http://dev.liveuser.6.cn/api/callBackSendSmsCode.php?';
	/**
	 *
	 */
	const PRO_SEND_CALLBACK_URL = 'http://liveuser/api/callBackSendSmsCode.php?';
	/**
	 *
	 */
	const GET_BALANCE_URL = 'http://liveuser/api/getBalanceInfo.php?appid=';

	/**
	 * @param array $data
	 *
	 * @return string
	 */
	public function sendMsg( array $data )
	{
		$data['appid'] = self::APPID;
		$data['mobile'] = "{$data['mobile']}";

		$sign          = $this->_createSign( $data );

		$data['sign']  = $sign;

		$str = http_build_query( $data );
		$env = strtoupper( $GLOBALS['env'] );

		if( $env == "PRO" )
		{
			$sendURL = self::PRO_SEND_URL . $str;
		}
		else
		{
			$sendURL = self::DEV_SEND_URL . $str;
		}

		$ret = file_get_contents( $sendURL );

		return $ret;
	}

	/**
	 * @param array $data
	 *
	 * @return string
	 */
	private function _createSign( array $data )
	{
		ksort( $data );

		return md5( json_encode( $data ) . self::KEY );
	}

	/**
	 * @param int $codeid
	 *
	 * @return string
	 */
	public function sendMsgCallBack( int$codeid )
	{
		$data = array(
			'appid'  => self::APPID,
			'codeid' => $codeid,
			'tm'     => (int)time()
		);

		$sign         = $this->_createSign( $data );
		$data['sign'] = $sign;

		$str = http_build_query( $data );
		$env = strtoupper( $GLOBALS['env'] );
		if( $env == "PRO" )
		{
			$sendURL = self::PRO_SEND_CALLBACK_URL . $str;
		}
		else
		{
			$sendURL = self::DEV_SEND_CALLBACK_URL . $str;
		}

		$ret = file_get_contents( $sendURL );

		return $ret;
	}

	/**
	 * @return mixed
	 */
	public function getMsgBalance()
	{
		$ret = file_get_contents(self::GET_BALANCE_URL.self::APPID);
		return json_decode($ret);
	}
}