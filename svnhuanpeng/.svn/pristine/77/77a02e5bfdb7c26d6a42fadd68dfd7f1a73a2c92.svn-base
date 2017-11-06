<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/8/17
 * Time: 13:27
 */

namespace HP\Op;

/**
 * Class WcsHelper
 * 网宿类
 * 说明
 *
 *
 */
class Wshelper
{

	/**
	 * @var bool $_debug 调试模式
	 */
	private $_debug = true;

	/**
	 * @var string $_logfile 日志文件
	 */
	private $_logfile = LOG_DIR . 'WcsHelper.error.log';

	/***********直播相关**************/

	/**
	 * PORTAL_USERNAME        portal平台账号
	 */
	const PORTAL_USERNAME = '6_huanpeng';

	/**
	 * PORTAL_PASSWD          portal平台密码
	 */
	const PORTAL_PASSWD = '6Huanpeng';

	/**
	 * FORBID_API            禁播API
	 */
	const FORBID_API = 'http://cm.chinanetcenter.com/CM/cm-command.do';

	/**
	 *  流查询
	 */
	const STREAM_API = 'http://qualiter.wscdns.com/api/streamStatusStatistic.jsp';

	/**
	 * 查流key
	 */
	const STREAM_KEY = 'F7C55786CE31EF9';

	/**
	 * WCS_AUTH_CALLBACK_SECRET           网宿推流鉴权key
	 */
	const WCS_AUTH_CALLBACK_SECRET = 'J$wd6lMtsE*PZuhP3E5!29SRly*!0MD2';

	/**
	 * WCS_STREAM_CALLBACK_SECRET          推流断流key
	 */
	const WCS_STREAM_CALLBACK_SECRET = 'OH3CuAU&p$uL8NG$N*6!^f#jg7cqP&H1';


	const WCS_NODE = 'liverecord';

	/************录像相关****************/

	/**
	 * AK           录像平台access
	 */
	const AK = '65a6d979f6d2f2a3ffaae8b62c91ac0aa8d5823a';

	/**
	 * SK           录像平台secret
	 */
	const SK = 'ca437c484e8a6340088be4f58a7302d20bc9b910';

	/**
	 * WCS_BUCKET_VIDEO  网宿录像空间
	 */
	const WCS_BUCKET_VIDEO = '6huanpeng-test001';

	/**
	 * WCS_BUCKET_LIVE   网宿直播空间
	 */
	const WCS_BUCKET_LIVE = 'huanpeng-jietu';

	/**
	 * WCS_MGR_API        录像API
	 */
	//const WCS_MGR_API = 'http://6huanpeng.mgr11.v1.wcsapi.com';
	const WCS_MGR_API = 'http://6huanpeng.mgr20.v1.wcsapi.com';

	/**
	 * CMD_CURL            http请求命令
	 */
	const CMD_CURL = '/usr/bin/curl -Ss -I';

	/**
	 * WS_SECURITY_CHAIN    网宿防盗链key
	 */
	const WS_SECURITY_CHAIN = '1234!@#$abcdef';

	/**
	 * CMD_MERGE        合并参数
	 */
	const CMD_MERGE = 'avconcat/mp4/mode/1/moovToFront/1/';

	/**
	 * CMD_TRANSCODE    转码参数
	 */
	const CMD_TRANSCODE = 'avthumb/mp4/moovToFront/1';

	/**
	 * CMD_POSTER        截图参数
	 */
	const CMD_POSTER = 'vframe/jpg/offset/';

	/**
	 * POSTER_SIZE_H         截图大小
	 */
	const POSTER_SIZE_H = 'w/1280/h/720';

	/**
	 * POSTER_SIZE_S         截图大小
	 */
	const POSTER_SIZE_S = 'w/720/h/1280';

	/**
	 * MERGE_NOTIFY_URL        录像回调通知url
	 */
	const WCS_VIDEO_NOTIFY_URL = 'wsMergeVideoCallBack.php';

	/**
	 * POSTER_NOTIFY_URL    录像海报回调通知url
	 */
	const WCS_POSTER_NOTIFY_URL = 'wsVideoPosterCallBack.php';


	/**
	 * @var array $saveDir 录像及海报保存路径
	 */
	private $_saveDir = array(
		'DEV' => array( 'v' => 'dev/v/', 'i' => 'dev/i/' ),
		'PRE' => array( 'v' => 'pre/v/', 'i' => 'pre/i/' ),
		'PRO' => array( 'v' => 'pro/v/', 'i' => 'pro/i/' )
	);

	/**
	 * 构造函数
	 */
	public function __construct()
	{

		//todo
	}


	/****************直播方法********************/

	/**
	 * 禁播
	 *
	 * @param  string $publishRtmpUrl 推流地址
	 *
	 * @return string                   错误码
	 */
	public function forbidLive( $publishRtmpUrl = NULL )
	{
		$params = $this->_getForbidPublishParams( $publishRtmpUrl );
		//mylog( self::FORBID_API . "?{$params}", LOG_DIR . 'Live.error.log' );
		$httBack = $this->_httpGet( self::FORBID_API . "?{$params}" );
		//mylog( $httBack['content'], LOG_DIR . 'Live.error.log' );
		return $httBack;
	}


	/**
	 * 网宿鉴权加密
	 *
	 * @param    array $data      加密参数
	 * @param    bool  $urlEncode 是否urlencode加密
	 *
	 * @return   string                  加密串
	 */
	public static function getWcsPublishLiveSecret( $data, $urlEncode = true )
	{
		$data = self::_paramsToString( $data );
		foreach ( $data as $key => $val )
		{
			$data[$key] = $urlEncode ? urlencode( $val ) : $val;
		}
		ksort( $data );
		$data = json_encode( $data, JSON_UNESCAPED_UNICODE );
		$sign = md5( sha1( $data . self::WCS_AUTH_CALLBACK_SECRET ) );

		return $sign;
	}


	/**
	 * 获取禁播参数
	 *
	 * @param  string $publishRtmpUrl 推流地址
	 *
	 * @return string                   禁播参数
	 */
	private function _getForbidPublishParams( $publishRtmpUrl = NULL )
	{
		$passWd = md5( self::PORTAL_USERNAME . self::PORTAL_PASSWD . $publishRtmpUrl );
		$data   = array(
			'username' => self::PORTAL_USERNAME,
			'password' => $passWd,
			'cmd'      => 'channel_manager',
			'action'   => 'forbid',
			'type'     => 'publish',
			'channel'  => $publishRtmpUrl
		);

		return http_build_query( $data );
	}


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
		$wsSecret = md5( WS_SECURITY_CHAIN . '/' . self::WCS_NODE . '/' . $filename . $cTime );
		$data     = array(
			'wsSecret' => $wsSecret,
			'eTime'    => $cTime
		);

		return http_build_query( $data );
	}

	/**
	 * 直播流链断、连回调加密串
	 *
	 * @param     array $data 回调参数数组
	 *
	 * @return    string            回调校验串
	 */
	public static function getWcsStreamCallBackSecret( $data )
	{
		$str = $data['stream'] . '_' . $data['ip'] . '_' . self::WCS_STREAM_CALLBACK_SECRET . '_' . $data['tm'];

		return md5( self::_paramsToString( $str ) );
	}


	/**
	 * http get请求
	 *
	 * @param  string $url 请求url
	 *
	 * @return string            get请求返回
	 */
	private function _httpGet( $url = NULL )
	{
		if( !$url )
		{
			return false;
		}

		$curl = new HttpHelper();
		$curl->addGet( $url, [], 5 );
		$result = $curl->getResult();
		return $result[0];
		//return @file_get_contents( $url );
	}

	function _wsHttpGet($url, $headers, $opt = null, $timeout=0)
	{
		$ch = curl_init();
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => true,
			CURLOPT_SSL_VERIFYHOST => 2,
			CURLOPT_HEADER => true,
			CURLOPT_NOBODY => false,
			CURLOPT_URL => $url,
			CURLOPT_TIMEOUT => $timeout
		);

		if($opt) {
			foreach ($opt as $key => $value) {
				$options[$key] = $value;
			}
		}

		if (!empty($headers)) {
			$options[CURLOPT_HTTPHEADER] = $headers;
		}

		curl_setopt_array($ch, $options);
		$result = curl_exec( $ch );

		$ret   = array();
		$errno = curl_errno( $ch );

		//错误状态码
		if( $errno !== 0 )
		{
			$ret['code']    = $errno;
			$ret['message'] = curl_error( $ch );
			curl_close( $ch );

			return $ret;
		}

		$code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		//分割响应头部和内容
		$responseArray     = explode( "\r\n\r\n", $result );
		$responseArraySize = sizeof( $responseArray );
		$respHeader        = $responseArray[$responseArraySize - 2];
		$respBody          = $responseArray[$responseArraySize - 1];

		$ret['code']       = $code;
		$ret['respHeader'] = $respHeader;
		$ret['respBody']   = $respBody;

		//超时判断
		if( $ret['code'] == 28 )
		{
			$ret['respBody'] = "请求超时！";
		}

		return $ret['respBody'];
	}

	/**
	 * http post请求
	 *
	 * @param  string $url     请求url
	 * @param  array  $headers 请求头
	 * @param  string $fields  请求内容
	 *
	 * @return string   http返回结果
	 */
	private function _httpPost( $url, $headers = null, $fields = null )
	{
		//var_dump( $url );
		//var_dump( $headers );
		//var_dump( $fields );
		$ch      = curl_init();
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HEADER         => true,
			CURLOPT_NOBODY         => false,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_URL            => $url,
			CURLOPT_TIMEOUT        => 0,
		);
		if( !empty( $headers ) )
		{
			$options[CURLOPT_HTTPHEADER] = $headers;
		}
		if( !empty( $fields ) )
		{
			$options[CURLOPT_POSTFIELDS] = $fields;
		}

		curl_setopt_array( $ch, $options );
		$result = curl_exec( $ch );

		$ret   = array();
		$errno = curl_errno( $ch );

		//错误状态码
		if( $errno !== 0 )
		{
			$ret['code']    = $errno;
			$ret['message'] = curl_error( $ch );
			curl_close( $ch );

			return $ret;
		}

		$code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
		curl_close( $ch );

		//分割响应头部和内容
		$responseArray     = explode( "\r\n\r\n", $result );
		$responseArraySize = sizeof( $responseArray );
		$respHeader        = $responseArray[$responseArraySize - 2];
		$respBody          = $responseArray[$responseArraySize - 1];

		$ret['code']       = $code;
		$ret['respHeader'] = $respHeader;
		$ret['respBody']   = $respBody;

		//超时判断
		if( $ret['code'] == 28 )
		{
			$ret['respBody'] = "请求超时！";
		}

		return $ret['respBody'];
	}

	/**
	 * 禁播返回错误处理
	 *
	 * @param    string $httpBack 禁播错误信息
	 *
	 * @return    array               禁播错误码及描述
	 */
	private function _getWcsForbidError( $httpBack = NULL )
	{
		//todo
	}


	/**
	 * 字符串转化
	 *
	 * @param    mixed $mix 待转化参数
	 *
	 * @return    mixed                      字符串化后的字符串或数组
	 */
	public static function _paramsToString( $mix )
	{
		if( is_string( $mix ) )
		{
			return $mix;
		}
		if( is_int( $mix ) || is_bool( $mix ) || is_float( $mix ) || is_double( $mix ) )
		{
			return "$mix";
		}
		if( is_array( $mix ) )
		{
			foreach ( $mix as $key => $v )
			{
				$mix[$key] = toString( $v );
			}

			return $mix;
		}

		return "";
	}


	/*********************录像方法****************************/

	/**
	 * 获取请求令牌
	 *
	 * @param      string $url  请求url
	 * @param      string $body 请求内容
	 *
	 * @return        string                    返回token
	 */
	private function _getToken( $url, $body = null )
	{
		$path  = parse_url( $url, PHP_URL_PATH );
		$query = parse_url( $url, PHP_URL_QUERY );
		if( $query )
		{
			if( $body )
			{
				$arr = array( $path, '?', $query, "\n", $body );
			}
			else
			{
				$arr = array( $path, '?', $query, "\n" );
			}
		}
		else
		{
			if( $body )
			{
				$arr = array( $path, "\n", $body );
			}
			else
			{
				$arr = array( $path, "\n" );
			}
		}
		$sign       = join( "", $arr );
		$encodesign = hash_hmac( 'sha1', $sign, self::SK, false );

		return self::AK . ':' . self::urlSafeBase64Encode( $encodesign );
	}

	/**
	 * 持久化操作
	 *
	 * @param  string $fops      操作命令参数
	 * @param  string $key       文件key
	 * @param  string $notifyURL 通知地址（选填，默认空）
	 * @param  int    $force     强制操作（选填，默认0）
	 * @param  int    $separate  分开通知（选填，默认0）
	 *
	 * @return       string          执行操作任务id（成功）或错误代码（失败）
	 */
	public function exec( $fops, $key, $notifyURL = null, $force = 0, $separate = 0 )
	{
		$url          = self::WCS_MGR_API . '/fops';
		$encodebucket = self::urlSafeBase64Encode( self::WCS_BUCKET_VIDEO );
		$body         = 'bucket=' . $encodebucket;
		/*//多文件拼接
		$keys = array_map( function ( $v )
		{
			return self::urlSafeBase64Encode( $v );
		}, $keys );
		$keys = implode( '/', $keys );*/
		//$keys = self::urlSafeBase64Encode( $keys );
		//var_dump( $keys );
		$body .= '&key=' .$key;
		$body .= '&fops=' . self::urlSafeBase64Encode( $fops );
		if( !empty( $notifyURL ) )
		{
			$body .= '&notifyURL=' . self::urlSafeBase64Encode( $notifyURL );
		}
		$body .= '&force=' . $force;
		$body .= '&separate=' . $separate;
		//print_r($body."\n");
		$headers = $this->_addHeader( $url, $body );

		$resp = $this->_httpPost( $url, $headers, $body );

		return $resp;
	}


	/**
	 * 拼接文件
	 *
	 * @param array  $files    合并文件
	 * @param string $saveFile 保存文件
	 *
	 * @return string                执行任务id或错误信息
	 */
	public function mergeFiles( $files, $saveFile )
	{
		if( !$files || !$saveFile )
		{
			return false;
		}
		if( !is_array( $files ) )
		{
			return $this->transcodeFile( $files, $saveFile );
		}
		$files = array_values(array_filter($files));
		if( count( $files ) < 2 )
		{
			return $this->transcodeFile( $files[0], $saveFile );
		}
		$primaryKey  = array_shift( $files );
		$primaryKey = self::urlSafeBase64Encode($primaryKey);
		//多文件拼接
		$keys = array_map( function ( $v )
		{
			return self::urlSafeBase64Encode( $v );
		}, $files );
		$keys = implode( '/', $keys );
		$saveFile    = self::WCS_BUCKET_VIDEO . ":" . $this->_saveDir[$GLOBALS['env']]['v'] . $saveFile;
		$saveFileOpt = "|saveas/" . self::urlSafeBase64Encode( $saveFile );

		$fops        = self::CMD_MERGE . $keys . $saveFileOpt;

		$notifyUrl = 'http://'.$GLOBALS['env-def'][$GLOBALS['env']]['domain-wsapi'].'/'.self::WCS_VIDEO_NOTIFY_URL;
		$ret         = $this->exec( $fops, $primaryKey, $notifyUrl );

		return $ret;
	}


	/**
	 * 文件转码
	 *
	 * @param string $file     文件名
	 * @param string $saveFile 保存文件
	 *
	 * @return string                             执行任务id或错误信息
	 */
	public function transcodeFile( $files, $saveFiles )
	{
		if( !$files || !$saveFiles )
		{
			return false;
		}
		$files = explode('/',$files);
		$saveFiles = explode('/',$saveFiles);
		if(count($files)!=count($saveFiles))
		{
			return false;
		}
		$fops = [];
		$keys = [];
		foreach ($files as $k=>$file)
		{
			$saveFile    = self::WCS_BUCKET_VIDEO . ":" . $this->_saveDir[$GLOBALS['env']]['v'] . $saveFiles[$k];
			$saveFileOpt = "|saveas/" . self::urlSafeBase64Encode( $saveFile );
			$fops[]        = self::CMD_TRANSCODE . $saveFileOpt;
			$keys[]        = $file;
		}
		$fops = implode(';',$fops);
		//todo
		$notifyUrl = 'http://'.$GLOBALS['env-def'][$GLOBALS['env']]['domain-wsapi'].'/'.self::WCS_VIDEO_NOTIFY_URL;
		$keys = array_map( function ( $v )
		{
			return self::urlSafeBase64Encode( $v );
		}, $keys );
		$keys = implode( '/', $keys );
		//$keys = self::urlSafeBase64Encode( $keys );
		$ret = $this->exec( $fops, $keys, $notifyUrl );

		return $ret;
	}

	/*public function transcodeFiles($files)
	{
		if(!is_array($files)||!count($files))
		{
			return false;
		}
		if(count($files)==1)
		{
			return $this->transcodeFile($files[0]['file'],$files[0]['saveFile']);
		}
		foreach ($files as $file)
		{

		}
	}*/

	/**
	 * 截图操作
	 *
	 * @param string $file     文件名
	 * @param string $saveFile 保存文件
	 * @param int    $offset   截图时间点
	 *
	 * @return string                        执行任务id或错误信息
	 */
	public function cutOutVideoPicture( $file, $saveFile, $param = null )
	{
		if( !$file || !$saveFile )
		{
			return false;
		}
		if( !$param )
		{
			$offset = 0;
			$size   = self::POSTER_SIZE_H;
		}
		else
		{
			$param  = explode( '/', $param );
			$offset = $param[1];
			$size   = $param[2] . '/' . $param[3] . '/' . $param[4] . '/' . $param[5];
		}
		$offset      = (int)$offset;
		$saveFile    = self::WCS_BUCKET_VIDEO . ":" . $this->_saveDir[$GLOBALS['env']]['i'] . $saveFile;
		$saveFileOpt = "|saveas/" . self::urlSafeBase64Encode( $saveFile );
		$fops        = self::CMD_POSTER . $offset . '/' . $size . $saveFileOpt;
		$notifyUrl = 'http://'.$GLOBALS['env-def'][$GLOBALS['env']]['domain-wsapi'].'/'.self::WCS_POSTER_NOTIFY_URL;
		$file = self::urlSafeBase64Encode($file);
		$ret = $this->exec( $fops, $file, $notifyUrl );

		return $ret;
	}

	/**
	 * 删除文件
	 *
	 * @param array|string $files     文件名
	 * @param string       $notifyURL 通知地址（选填，默认空）
	 * @param int          $force     强制操作（选填，默认0）
	 * @param int          $separate  分开通知（选填，默认0）
	 *
	 * @return string                        执行任务id或错误信息
	 */
	public function deleteFiles( $files, $notifyURL = null, $force = 0, $separate = 0 )
	{
		if( !$files )
		{
			return false;
		}
		$url    = self::WCS_MGR_API . "/fmgr/delete";
		$bucket = self::urlSafeBase64Encode( self::WCS_BUCKET_VIDEO );
		$body   = "fops=";
		if( is_array( $files ) )
		{
			$files = array_map( function ( $v ) use ( $bucket )
			{
				return "bucket/" . $bucket . "/key/" . self::urlSafeBase64Encode( $v );
			}, $files );
			$files = implode( ";", $files );
		}
		else
		{
			$files = "bucket/" . $bucket . "/key/" . self::urlSafeBase64Encode( $files );
		}
		$body .= $files;
		if( !empty( $notifyURL ) )
		{
			$body .= '&notifyURL=' . self::urlSafeBase64Encode( $notifyURL );
		}
		$body .= '&force=' . $force;
		$body .= '&separate=' . $separate;
		$headers = $this->_addHeader( $url, $body );
		$resp    = $this->_httpPost( $url, $headers, $body );

		return $resp;
	}


	/**
	 * 获取下载链接
	 *
	 * @param  string $url url链接
	 *
	 * @return string            下载链接
	 */
	public static function getDownloadUrl( $url, $http = '' )
	{
		if( !$url )
		{
			return '';
		}
		$url        = trim( $url );
		$httpStatus = self::getHttpStatus( $url );
		if( $httpStatus == '200' )
		{
			return $url;
		}
		if( $httpStatus == '302' )
		{
			$url = self::getLocationUrl( $url );

			//$this->getDownUrl($url)
			return self::getDownloadUrl( $url );
		}
		if( $httpStatus == '403' && $httpStatus != $http )
		{
			$url    = explode( '?', $url );
			$url    = $url[0];
			$secret = self::getSecurityUrl( $url );

			//return $url."?{$secret}";
			return self::getDownloadUrl( $url . "?{$secret}", $httpStatus );
		}

		return '';
	}


	/**
	 * 获取跳转链接
	 *
	 * @param    string $url url
	 *
	 * @return  string                跳转链接
	 */
	public static function getLocationUrl( $url )
	{
		$cmd = self::CMD_CURL . " \"{$url}\"";
		$res = `$cmd`;
		$pat = '/HTTP\/1.+(302)[\s\S]*Location:(.*)\r\n/';
		$r   = preg_match( $pat, $res, $mat );
		if( !$mat[2] )
		{
			return '';
		}

		return $mat[2];
	}


	/**
	 * 获取http状态码
	 *
	 * @param   string $url url
	 *
	 * @return    string                http状态码
	 */
	public static function getHttpStatus( $url )
	{
		$cmd = self::CMD_CURL . " \"{$url}\"";
		$res = `$cmd`;
		$pat = '/HTTP\/1.+([0-9]{3})[\S\s]/';
		$r   = preg_match( $pat, $res, $mat );
		if( isset( $mat[1] ) )
		{
			return $mat[1];
		}

		return '';
	}


	/**
	 * 获取防盗链加密参数
	 *
	 * @param    string $filename 文件名
	 *
	 * @return string                防盗链串
	 *
	 */
	public static function getSecurityUrl( $filename )
	{
		$filename = basename( $filename );
		$now      = time();
		$time     = dechex( $now );
		//$cTime    = dechex( $now );
		$wsSecret = md5( self::WS_SECURITY_CHAIN . '/' . $filename . $time );
		$data     = array(
			'wsSecret' => $wsSecret,
			'eTime'    => $time
		);

		return http_build_query( $data );
	}

	/**
	 * 添加请求认证头
	 *
	 * @param string $url     url
	 * @param string $content 添加的内容
	 *
	 * @return string                        请求认证头信息
	 */
	private function _addHeader( $url, $content=null )
	{
		$token   = $this->_getToken( $url, $content );
		$headers = array( "Authorization:$token" );

		return $headers;
	}

	/**
	 * url base64
	 *
	 * @param  string $str url
	 *
	 * @return string              返回base64后的url
	 */
	public static function urlSafeBase64Encode( $str )
	{
		$find    = array( '+', '/' );
		$replace = array( '-', '_' );

		return str_replace( $find, $replace, base64_encode( $str ) );
	}

	/**
	 * 获取直播流信息
	 * @param string $domain
	 *
	 * @return bool
	 */
	public static function getWsStreamInfoByApi( $domain = '' ){
		if( !$domain )
		{
			return false;
		}
		$n = self::PORTAL_USERNAME;
		$r = time();
		$key = self::STREAM_KEY;
		$k = md5( $r.$key );
		$u = $domain;
		$params = [
			'n' => $n,
			'r' => $r,
			'k' => $k,
			'u' => $u
		];
		$curl = new HttpHelper();
		$curl->addGet( self::STREAM_API, $params);
		$results = $curl->getResult();
		return $results[0];
	}

	/**
	 * 列举资源
	 * @param      $bucket
	 * @param int  $limit
	 * @param null $prefix
	 * @param null $mode
	 * @param null $marker
	 *
	 * @return Response
	 */
	public function bucketList($bucket, $limit = 1000, $prefix = null, $mode = null, $marker = null)
	{
		$bucket = self::WCS_BUCKET_VIDEO;
		$path = '/list';
		$path .= "?bucket=$bucket";
		$path .= "&limit=$limit";
		if($prefix !== null) {
			$prefix = self::urlSafeBase64Encode($prefix);
			$path.= "&prefix=$prefix";
		}
		if($mode !== null) {
			$path .= "&mode=$mode";
		}
		if($marker !== null) {
			$path .= "&marker=$marker";
		}

		$url = self::WCS_MGR_API . $path;
		$headers = $this->_addHeader($url);
		$resp = $this->_wsHttpGet($url, $headers);
		return $resp;
	}

	public function stat($file)
	{
		$url = self::WCS_MGR_API . '/stat/';
		$fops = self::WCS_BUCKET_VIDEO . ":" . $file;
		$fops = self::urlSafeBase64Encode($fops);
		$url .= $fops;
		$headers = $this->_addHeader($url);
		$resp = $this->_wsHttpGet($url, $headers);
		return $resp;
	}

	public function deletePrefix($prefix)
	{
		if(!$prefix)
			return false;
		$fops = 'fops=bucket/' . self::urlSafeBase64Encode(self::WCS_BUCKET_VIDEO) ;
		$fops .= '/prefix/' . self::urlSafeBase64Encode($prefix);
		$url = self::WCS_MGR_API . '/fmgr/deletePrefix';
		$header = $this->_addHeader($url,$fops);
		$resp = $this->_httpPost($url,$header,$fops);
		return $resp;
	}
	public function buckets()
	{
		$url = self::WCS_MGR_API . '/bucket/list';
		$header = $this->_addHeader($url);
		$resp = $this->_wsHttpGet($url,$header);
		return $resp;
	}
}