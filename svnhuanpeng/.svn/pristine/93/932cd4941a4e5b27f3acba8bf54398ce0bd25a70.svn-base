<?php

/**请求前台数据处理类
 * Created by PhpStorm.
 * User: dong
 * Date: 17/4/21
 * Time: 下午2:27
 */
class publicRequist
{

	private static $setRate_path = '/api/public/finance/setRate.php'; //比率变更
	private static $recharge_path = '/api/public/finance/innerRecharge.php'; //内部发放
	private static $rateSecretKey = 'hW0cBDMFw5dfLSZ9';//更改比率加密字符串

	/**
	 * 汇率改变通知财务系统
	 *
	 * @param  array $list array('uid'=>rid) 用户ID=>记录id
	 * @param int    $rate 比率
	 * @param string $desc 描述
	 *
	 * @return bool|mixed
	 */
	public static function outside_setRate( $list, $rate, $desc )
	{
		$url = $GLOBALS['env-def'][$GLOBALS['env']]['outside-domain'] . static::$setRate_path;
		$data = array(
			'list' => $list,
			'rate' => $rate,
			'desc' => $desc
		);
		$res = self::curl_post( $url, $data );
		return $res;
	}

	/**内部发放
	 *
	 * @param  int    $uid       用户id
	 *    $data=array(
	 *            'uid'
	 *            'hpbean'
	 *            'hpcoin'
	 *            'coin'
	 *            'bean'
	 *            'desc'
	 *            'activeid'
	 *            'recordid'
	 *            );
	 *
	 * @return bool|mixed
	 */
	public static function outside_recharge( $data )
	{
		$url = $GLOBALS['env-def'][$GLOBALS['env']]['outside-domain'] . static::$recharge_path;
		$res = self::curl_post( $url, $data );
		return $res;
	}


	private function curl_post( $url, $data = array() )
	{
		//对空格进行转义
		$url = str_replace( ' ', '+', $url );
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, "$url" );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_HEADER, 0 );
		curl_setopt( $ch, CURLOPT_TIMEOUT, 5 ); //定义超时3秒钟
		curl_setopt( $ch, CURLOPT_POST, 1 );
		curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
		$output = curl_exec( $ch );
		$errorCode = curl_errno( $ch );
		curl_close( $ch );
		if( 0 !== $errorCode )
		{
			return false;
		}
		return $output;
	}
}
