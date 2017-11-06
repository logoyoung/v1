<?php
/**
 * Created by PhpStorm.
 * User: logoyoung
 * Date: 17/5/10
 * Time: 10:38
 */
/********************************直播相关缓存队列服务**********************************************/


namespace lib;


use \RedisHelp;
use lib\Video;

class LiveCacheBat
{

	private $_redis;
	private $_video;

	const MERGE_BUFFER        = 'redis_buffer_merge';
	const TRANSCODE_BUFFER    = 'redis_buffer_transcode';
	const VIDEO_POSTER_BUFFER = 'redis_buffer_video_poster';
	const LIVE_POSTER_BUFFER  = 'redis_buffer_live_poster';

	const CONSUMER_SIZE_DEFAULT = 20;

	public function __construct( $redis = null,$video=null )
	{
		if( !$redis )
		{
			$redis = self::getRedis();
		}
		$this->_redis = $redis->getMyRedis();
		if(!$video)
		{
			$video = self::getVideo();
		}
		$this->_video = $video;
	}

	private function _init()
	{

	}


	public static function doBat()
	{
		return Video::CACHE;
	}

	public static function getRedis()
	{
		return new RedisHelp();
	}

	public static function getVideo()
	{
		return new Video();
	}

	/**
	 * 生产任务
	 *
	 * @param $buffer
	 * @param $value
	 *
	 * @return bool
	 */
	public function producer($buffer,$value)
	{
		return $this->_redis->rpush($buffer,$value);
	}

	/**
	 * 消费任务
	 *
	 * @param $buffer
	 * @param $value
	 *
	 * @return string
	 */
	public function consumer($buffer)
	{
		return $this->_redis->lpop($buffer);
	}

	public function bufferLen($buffer)
	{
		return $this->_redis->lLen($buffer);
	}
	/**
	 * 挂起状态
	 */
	public function wait()
	{
		//sleep(1);
	}

	public function produceMergeTask($files)
	{
		return $this->producer(self::MERGE_BUFFER,$files);
	}

	public function consumeMergeTask($size = self::CONSUMER_SIZE_DEFAULT)
	{
		$len = $this->bufferLen(self::MERGE_BUFFER);
		$size = min($size,$len);
		$buffer = [];
		for($i=0;$i<$size;$i++)
		{
			$buffer[] = $this->consumer(self::MERGE_BUFFER);
		}
		//todo merge batche
	}

	public function produceTanscodeTask($files)
	{
		//检测下是否可生产
		//todo
		return $this->producer(self::TRANSCODE_BUFFER,$files);
	}

	public function consumeTanscodeTask($size = self::CONSUMER_SIZE_DEFAULT)
	{
		$len = $this->bufferLen(self::TRANSCODE_BUFFER);
		$size = min($size,$len);
		$buffer = [];
		for($i=0;$i<$size;$i++)
		{
			$buffer[] = $this->consumer(self::TRANSCODE_BUFFER);
		}
		if(!count($buffer))
		{
			return false;
		}
		//todo transcode batche
		foreach ($buffer as $task)
		{
			$task = explode('/',$task);
			$files[] = $task[0];
			$saveFiles[] = $task[1];
			$taskIDs[] = $task[2];
		}
		$files = implode('/',$files);
		$saveFiles = implode('/',$saveFiles);
		$ret = $this->_video->transcodeFile($files,$saveFiles,false);
		//error todo
		$ret = json_decode($ret,true);
		//var_dump($ret['persistentId']);
		return $this->_video->updateTaskIDs($ret['persistentId'],$taskIDs);
	}


	public function produceVideoPosterTask()
	{

	}

	public function consumeVideoPosterTask()
	{

	}

	public function produceLivePosterTask($value)
	{
		//队列检测
		//todo
		return $this->producer(self::LIVE_POSTER_BUFFER,$value);
	}

	public function consumeLivePosterTask($size=self::CONSUMER_SIZE_DEFAULT)
	{

	}

	public function produceLiveTask()
	{

	}

	public function consumeLiveTask()
	{

	}

}