<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/3/17
 * Time: 10:33
 */

/******************************说明**************************************
 * 网宿视频文件操作类
 * 合并
 * 转码
 * 截图
 * 删除
 * 下载
 *************************************************************************/

namespace Wcs\PersistentFops;

use Wcs;
use Wcs\Config;

/*use Wcs\PersistentFops\Fops;*/
include_once 'Fops.php';

class WsVideoHelp extends Fops
{

	//http请求命令
	const CMD_CURL = '/usr/bin/curl -Ss -I';
	//网宿key
	const WS_SECURITY_CHAIN = '1234!@#$abcdef';
	//合并
	const CMD_MERGE = 'avconcat/mp4/mode/1/moovToFront/1/';
	//转码
	const CMD_TRANSCODE = 'avthumb/mp4/moovToFront/1';
	//截图
	const CMD_POSTER = 'vframe/jpg/offset/';
	//截图尺寸
	const POSTER_WIDTH  = 1900;
	const POSTER_HEIGHT = 1069;

	//网速参数
	const FORCE   = 0;
	const SAPARET = 0;

	//转码格式
	const VIDEO_EXT = 'mp4';
	//图片格式
	const POSTER_EXT = 'jpg';
	//合并操作
	const T_MERGE = 'MERGE';
	//截图操作
	const T_POSTER = 'POSTER';
	//转码操作
	const T_TRANSCODE = 'TRANSCODE';


	// private $bucket = null;
	private $liveid = null;
	private $mergeKeys = null;
	private $duration = 0;
	private $primaryKey = '';


	private $separate = 1;
	//private $force = 2;


	public $fops = '';

	//保存路径
	private $saveDir = array(
		'DEV' => array( 'v' => 'dev/v/', 'i' => 'dev/i/' ),
		'PRO' => array( 'v' => 'pro/v/', 'i' => 'pro/i/' )
	);

	public function __construct( $auth, $bucket )
	{
		parent::__construct( $auth, $bucket );
	}

	/**
	 * 合并操作
	 *
	 * @param $data
	 * @param $notifyUrl
	 *
	 * @return mixed
	 */
	public function merge( $data, $notifyUrl )
	{
		return $this->process( $data, $notifyUrl, self::T_MERGE );
	}

	/**
	 * 转码操作
	 *
	 * @param $data
	 * @param $notifyUrl
	 *
	 * @return mixed
	 */
	public function transcode( $data, $notifyUrl )
	{
		return $this->process( $data, $notifyUrl, self::T_TRANSCODE );
	}

	/**
	 * 截图操作
	 *
	 * @param $data
	 * @param $notifyUrl
	 *
	 * @return mixed
	 */
	public function poster( $data, $notifyUrl )
	{
		return $this->process( $data, $notifyUrl, self::T_POSTER );
	}

	/**
	 * 实现命令操作
	 *
	 * @param $data
	 * @param $notifyUrl
	 * @param $type
	 *
	 * @return mixed
	 */
	public function process( $data, $notifyUrl, $type )
	{
		$this->setInfo( $data );
		$fops = $this->getFops( $type );//var_dump($fops);
		//$keysBase64 = $this->getBase64Keys($this->keys);
		$ret = $this->exec( $fops, $this->primaryKey, $notifyUrl );

		return $ret;
	}

	/**
	 * base64加密
	 *
	 * @param $data
	 *
	 * @return array|bool|mixed|string
	 */
	private function getBase64Keys( $data )
	{
		if( !$data )
		{
			return false;
		}
		if( is_string( $data ) )
		{
			//$v = explode(':',$data);
			//$v = isset($v[1])?$v[1]:$v[0];
			$v = $this->getRealKey( $data );

			return \Wcs\url_safe_base64_encode( $v );
		}
		$keysBase64 = array();
		foreach ( $data as $k => $v )
		{
			//$v = explode(':',$v);
			$v              = $this->getRealKey( $v );
			$keysBase64[$k] = \Wcs\url_safe_base64_encode( $v );
		}
		$keysBase64 = implode( '/', $keysBase64 );

		return $keysBase64;
	}


	/**
	 * 获取命令参数
	 *
	 * @param $ext
	 *
	 * @return bool|string
	 */
	private function getFops( $ext )
	{
		if( $ext == self::T_MERGE )
		{
			return self::CMD_MERGE . $this->mergeKeys . $this->getSaveFileName( self::VIDEO_EXT );
		}
		else if( $ext == self::T_POSTER )
		{
			$t = (int)( $this->duration / 2 ) . '/w/' . self::POSTER_WIDTH . '/h/' . self::POSTER_HEIGHT;

			return self::CMD_POSTER . $t . $this->getSaveFileName( self::POSTER_EXT );
		}
		else if( $ext == self::T_TRANSCODE )
		{
			return self::CMD_TRANSCODE . $this->getSaveFileName( self::VIDEO_EXT );
		}
		else
		{
			return false;
		}
	}

	/**
	 *获取保存路径
	 *
	 * @param $ext
	 *
	 * @return string
	 */
	private function getSaveFileName( $ext )
	{
		$i    = ( $ext == self::VIDEO_EXT ) ? 'v' : 'i';
		$file = $this->bucket . ":" . $this->saveDir[$GLOBALS['env']][$i] . $this->liveid . "." . $ext;
		var_dump( $file );

		return "|saveas/" . \Wcs\url_safe_base64_encode( $file );
	}

	/**
	 * 配置录像文件信息
	 *
	 * @param $data
	 *
	 * @return bool
	 */
	private function setInfo( $data )
	{
		if( !$data || !is_array( $data ) || !$data['keys'] )
		{
			return false;
		}
		$this->bucket     = $data['bucket'];
		$this->liveid     = $data['liveid'];
		$data['duration'] = ( isset( $data['duration'] ) && $data['duration'] ) ? (int)$data['duration'] : $this->duration;
		$this->duration   = $data['duration'];

		if( count( $data['keys'] ) == 1 )
		{
			$realKey          = $this->getRealKey( $data['keys'][0] );
			$this->primaryKey = \Wcs\url_safe_base64_encode( $realKey );//base64
			return true;
		}
		$realKey          = $this->getRealKey( array_shift( $data['keys'] ) );
		$this->primaryKey = \Wcs\url_safe_base64_encode( $realKey );//base64
		$this->mergeKeys  = $this->getBase64Keys( $data['keys'] );

		return true;
	}

	/**
	 * 获取操作具体文件
	 *
	 * @param $str
	 *
	 * @return array
	 */
	private function getRealKey( $str )
	{
		$key = explode( ':', $str );
		$key = isset( $key[1] ) ? $key[1] : $key[0];

		return $key;
	}

	/**
	 * 获取下载链接
	 *
	 * @param null $url
	 *
	 * @return null|string
	 */
	public function getDownUrl( $url, $http = '' )
	{
		if( !$url )
		{
			return '';
		}
		$url        = trim( $url );
		$httpStatus = self::getHttpStatus( $url );
		var_dump( $httpStatus );
		if( $httpStatus == '200' )
		{
			return $url;
		}
		if( $httpStatus == '302' )
		{
			$url = self::getLocationUrl( $url );

			//$this->getDownUrl($url)
			return self::getDownUrl( $url );
		}
		if( $httpStatus == '403' && $httpStatus != $http )
		{
			$url    = explode( '?', $url );
			$url    = $url[0];
			$secret = self::getSecuritUrl( $url );

			//return $url."?{$secret}";
			return self::getDownUrl( $url . "?{$secret}", $httpStatus );
		}

		return '';
	}

	/**
	 * 获取跳转链接
	 *
	 * @param $url
	 *
	 * @return string
	 */
	public function getLocationUrl( $url )
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
	 * @param $url
	 *
	 * @return mixed
	 */
	public function getHttpStatus( $url )
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
	 * 获取加密参数
	 *
	 * @param $filename
	 *
	 * @return string
	 *
	 */
	public function getSecuritUrl( $filename )
	{
		$filename = basename( $filename );
		$now      = time();
		//$eTime = dechex($now+WS_EXPIRED);
		$eTime    = dechex( $now );
		$cTime    = dechex( $now );
		$wsSecret = md5( self::WS_SECURITY_CHAIN . '/' . $filename . $cTime );
		$data     = array(
			'wsSecret' => $wsSecret,
			'eTime'    => $eTime
		);

		return http_build_query( $data );
	}

	/**
	 * 删除资源,fops格式如下
	 *fops=bucket/<Urlsafe_Base64_Encoded_bucket>
	 * /key/<Urlsafe_Base64_Encoded_key>
	 * &notifyURL =<Urlsafe_Base64_Encoded_notifyUrl>
	 * &eparate=<Separate>
	 *
	 * @param $fops
	 *
	 * @return mixed
	 */
	public function delete( $file )
	{
		$fops    = $this->getDelOps( $file );
		$url     = Config::WCS_MGR_URL . "/fmgr/delete";
		$content = $this->_addContent( $fops );
		$headers = $this->_genernate_header( $url, $content );
		$resp    = $this->_post( $url, $headers, $content );
		var_dump( $url );
		var_dump( $content );
		var_dump( $headers );

		return $resp;
	}

	/**
	 * @param $file
	 *
	 * @return string
	 */
	public function getDelOps( $file )
	{
		$bucket = \Wcs\url_safe_base64_encode( $this->bucket );
		if( is_string( $file ) )
		{
			$key  = \Wcs\url_safe_base64_encode( $file );
			$fops = "fops=bucket/$bucket/key/$key";
		}
		if( is_array( $file ) )
		{
			$fops = "fops=";
			foreach ( $file as $k => $v )
			{
				if( $fops != 'fops=' )
				{
					$fops .= ";";
				}
				$key = \Wcs\url_safe_base64_encode( $v );
				$fops .= "bucket/$bucket/key/$key";
			}
		}
		//$bucket = \Wcs\url_safe_base64_encode($this->bucket);
		//var_dump($this->bucket.":".$file);

		//var_dump($fops);
		//return "fops=bucket/$bucket/key/$key";
		//$fops = \Wcs\url_safe_base64_encode($this->bucket.":".$file);
		return $fops;
	}

	/**
	 * @param $fops
	 *
	 * @return int|string
	 */
	private function _addContent( $fops )
	{

		$this->fops = $fops;
		$content    = $this->fops;
		if( !empty( $this->notifyURL ) )
		{
			$content .= "&notifyURL=" . \Wcs\url_safe_base64_encode( $this->notifyURL );
		}
		//$content .= "&force=" . $this->force;
		//$content .= "&separate=" . $this->separate;

		return $content;
	}

}


/**********************************test***********************************/

//合并测试
//转码测试
//截图测试
//删除测试

//下载测试
/*$url = 'http://fvod.huanpeng.com/8780.mp4';
$url = WsVideoHelp::getDownUrl($url);
echo "-------\n";
var_dump($url);*/

