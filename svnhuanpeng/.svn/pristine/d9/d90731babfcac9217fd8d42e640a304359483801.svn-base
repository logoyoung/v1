<?php

/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/1
 * Time: 14:40
 */
namespace service\login;

use system\RedisHelper;

class Timer
{
	const REDIS_TIME_OUT = 600;

	private $timer;

	private $redisObj;

	private $timerRedisKey;

	private static $instance = [];

	public function __construct( $phone )
	{
		$this->timerRedisKey = "LoginNumber:$phone";

		$timer = $this->getRedisObj()->get( $this->timerRedisKey );

		$this->timer = intval( $timer );
	}

	/**
	 * @param $phone
	 *
	 * @return Timer
	 */
	public static function getInstance( $phone )
	{
		if ( !isset( self::$instance[$phone] ) )//&& $phone )
		{
			self::$instance[$phone] = new Timer( $phone );
		}

		return self::$instance[$phone];
	}

	/**
	 * @return mixed
	 */
	public function getRedisObj(): \system\RedisConnection
	{
		if ( !$this->redisObj )
		{
			$this->redisObj = RedisHelper::getInstance( 'huanpeng' );
		}

		return $this->redisObj;
	}

	/**
	 * @return mixed
	 */
	public function get()
	{
		return $this->timer;
	}

	/**
	 *
	 */
	public function add()
	{
		$this->timer++;
	}

	/**
	 *
	 */
	public function clear()
	{
		$this->timer = 0;
	}

	function __destruct()
	{
		$this->getRedisObj()->set( $this->timerRedisKey, $this->timer, self::REDIS_TIME_OUT );
	}

}