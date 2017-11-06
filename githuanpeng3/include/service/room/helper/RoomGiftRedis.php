<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/9/17
 * Time: 下午9:41
 */

namespace service\room\helper;


use system\RedisHelper;

class RoomGiftRedis
{
	const ROOM_GIFT_CONFIG = 'huanpeng';

	const ROOM_GIFT_CONFIGID_MARK_PRE = "GIFT_CONFIGID_MARK";//记录配置的排序，数量，每个礼物的mark的md5加密
	const ROOM_GIFT_CONFIGID_INFO_PRE = "GIFT_CONFIGID_INFO";

	const ROOM_GIFT_MASTER_CONFIG_ID = -1;
	const DEFAULT_EXPIRE          = 0;

	static $self = '';

	public static function getInstance()
	{
		if ( !static::$self )
		{
			static::$self = new RoomGiftRedis();
		}

		return static::$self;

	}

	public function getRedis()
	{
		return RedisHelper::getInstance( self::ROOM_GIFT_CONFIG );
	}

	public function getConfigMarkKey( $configId )
	{
		return self::ROOM_GIFT_CONFIGID_MARK_PRE . "_" . $configId;
	}

	public function setConfigMark( $configId, $mark, $expire = RoomGiftRedis::DEFAULT_EXPIRE )
	{
		$key = $this->getConfigMarkKey( $configId );

		$try = 2;
		do
		{
			$status = $this->getRedis()->set( $key, $mark );

			if ( $status )
			{
				if ( $expire )
				{
					$this->getRedis()->expire( $key, $expire );
				}

				return true;
			}

			usleep( 1 );
		} while ( $try-- > 0 );

		return false;
	}

	public function setConfigMark_AllGift( $mark, $expire = RoomGiftRedis::DEFAULT_EXPIRE )
	{
		return $this->setConfigMark( self::ROOM_GIFT_MASTER_CONFIG_ID, $mark, $expire );
	}

	public function getConfigMark( $configId )
	{
		$key = $this->getConfigMarkKey( $configId );

		if ( !$this->getRedis()->exists( $key ) )
		{
			return null;
		}

		$result = $this->getRedis()->get( $key );

		if ( $result !== false )
		{
			return $result;
		}

		return false;
	}

	public function getConfigMark_AllGift()
	{
		return $this->getConfigMark( self::ROOM_GIFT_MASTER_CONFIG_ID );
	}

	public function getConfigInfoKey( $configId )
	{
		return self::ROOM_GIFT_CONFIGID_INFO_PRE . "_" . $configId;
	}

	public function setConfigInfo( $configId, array $data, $expire = RoomGiftRedis::DEFAULT_EXPIRE )
	{
		$key = $this->getConfigInfoKey( $configId );

		$try = 2;
		do
		{
			$status = $this->getRedis()->set( $key, json_encode( $data ) );
			if ( $status )
			{
				if ( $expire )
				{
					$this->getRedis()->expire( $key, $expire );
				}

				return true;
			}

			usleep( 1 );
		} while ( $try-- > 0 );

		return false;

	}

	public function setConfigInfo_AllGift( array $data, $expire = RoomGiftRedis::DEFAULT_EXPIRE )
	{
		return $this->setConfigInfo( self::ROOM_GIFT_MASTER_CONFIG_ID, $data, $expire );
	}

	public function getConfigInfo( $configId )
	{
		$key = $this->getConfigInfoKey( $configId );

		if ( !$this->getRedis()->exists( $key ) )
		{
			return null;
		}

		$result = $this->getRedis()->get( $key );

		if ( $result !== false )
		{
			return json_decode( $result, true );
		}

		return false;
	}

	/**
	 * 获取所有礼物的详情
	 *
	 * 里面包含具体的礼物信息，不同于普通获取，普通获取仅仅获取礼物的id，以及mark值
	 * @return bool|mixed|null
	 */
	public function getConfigInfo_ALLGift()
	{
		return $this->getConfigInfo( self::ROOM_GIFT_MASTER_CONFIG_ID );
	}


}