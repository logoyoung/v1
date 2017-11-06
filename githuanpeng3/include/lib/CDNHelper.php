<?php

namespace lib;

use lib\WcsHelper;


/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/3/31
 * Time: 15:10
 */

/**
 * CDN类
 *
 *
 */
class CDNHelper
{

	/**
	 * @var $_helper  CND具体操作对象
	 */
	private $_helper;

	//构造函数
	public function __construct( $WcsHelper = null )
	{
		if( !$WcsHelper )
		{
			$WcsHelper = new WcsHelper();
		}
		$this->_helper = $WcsHelper;
	}

	/**
	 * 主动切断网宿直播流
	 *
	 * @param string $publishRtmpUrl 推流地址
	 *
	 * @return string                       错误码
	 */
	public function stopCDNStream( $publishRtmpUrl )
	{
		return $this->_helper->forbidLive( $publishRtmpUrl );
	}

	/**
	 * 网宿鉴权加密
	 *
	 * @param  array $data      加密参数
	 * @param  bool  $urlEncode 是否urlencode加密
	 *
	 * @return string                加密串
	 */
	public static function getPublishLiveSecret( $data, $urlEncode = true )
	{
		return WcsHelper::getWcsPublishLiveSecret( $data, $urlEncode );
	}


	/**
	 * 获取网宿拉流防盗链加密串
	 *
	 * @param  string $stream 加密参数
	 *
	 * @return string                加密串
	 */
	public static function getPlayLiveSecret( $stream )
	{
		return WcsHelper::getWcsPlayLiveSecret( $stream );
	}

	/**
	 * 获取直播流回调加密串
	 *
	 * @param  array $data 加密参数
	 *
	 * @return string                加密串
	 */
	public static function getStreamCallBackSecret( $data )
	{
		return WcsHelper::getWcsStreamCallBackSecret( $data );
	}

	/**
	 * 删除文件
	 *
	 * @param  string|array $files 文件名
	 *
	 * @return string                        执行任务id或错误码
	 */
	public function deleteFiles( $files )
	{
		return $this->_helper->deleteFiles( $files );
	}

	/**
	 * 拼接文件
	 *
	 * @param  array  $files    文件列表
	 * @param  string $saveFile 保存文件
	 *
	 * @return string                          执行任务id或错误码
	 */
	public function mergeFiles( $files, $saveFile )
	{
		return $this->_helper->mergeFiles( $files, $saveFile );
	}


	/**
	 * 视频文件转码
	 *
	 * @param  string $file     文件名
	 * @param  string $saveFile 保存文件
	 *
	 * @return string                            执行任务id或错误码
	 */
	public function transcodeFile( $file, $saveFile )
	{
		return $this->_helper->transcodeFile( $file, $saveFile );
	}


	/**
	 * 视频截图
	 *
	 * @param string $file     文件名
	 * @param string $saveFile 保存文件
	 * @param string $offset   截取时间位置
	 *
	 * @return string                         执行任务id或错误码
	 */
	public function cutOutVideoPicture( $file, $saveFile, $offset )
	{
		return $this->_helper->cutOutVideoPicture( $file, $saveFile, $offset );
	}


	/**
	 * 文件下载
	 *
	 * @param $url                文件名
	 *
	 * @return string|false        下载链接｜操作失败
	 */
	public static function getDownloadUrl( $url )
	{
		return WcsHelper::getDownloadUrl( $url );
	}

}