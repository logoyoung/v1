<?php
// +----------------------------------------------------------------------
// 流媒体加密
// +----------------------------------------------------------------------

namespace HP\Secure;

class WsSrc
{


	const WCS_NODE = 'liverecord';
	const WS_SECURITY_CHAIN = '1234!@#$abcdef';
	const WS_DEFER = 30;

	/**
	 * 获取网宿加密串
	 *
	 * @param   string $filename 文件名或流名
	 *
	 * @return  string            加密串
	 */
	public static function getWcsPlayLiveSecret( $filename )
	{
		$now      = time()-30;
		$cTime    = dechex( $now );
		$wsSecret = md5( self::WS_SECURITY_CHAIN . '/' . self::WCS_NODE . '/' . $filename . $cTime );
		$data     = array(
			'wsSecret' => $wsSecret,
			'eTime'    => $cTime
		);

		return http_build_query( $data );
	}
}
