<?php

namespace lib\chatRoom;
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/9/13
 * Time: 下午1:54
 */

class ChatRoom
{
	private        $uid;
	private        $luid;
	private static $db;


	/**
	 * @param mixed $luid
	 */
	public function setLuid( $luid )
	{
		$this->luid = $luid;
	}

	/**
	 * @param mixed $uid
	 */
	public function setUid( $uid )
	{
		$this->uid = $uid;
	}

	public function getChatRoomIdByUid( $getAll = false )
	{

		if ( !$this->uid )
		{
			return [];
		}

		if ( $getAll )
		{
			$sql = "select DISTINCT(luid) as luid from liveroom where uid =:uid";
		}
		else
		{
			$sql = "select DISTINCT(luid) as luid from liveroom where luid != 1 and uid=:uid";
		}

		$data = [ 'uid' => $this->uid ];

		try
		{
			$luids  = [];
			$result = $this->getDB()->query( $sql, $data );
			if ( is_array( $result ) )
			{
				foreach ( $result as $tmp )
				{
					array_push( $luids, $tmp['luid'] );
				}

				return $luids;
			}
			else
			{
				return [];
			}
		} catch ( \Exception $exception )
		{
			//todo log
			return false;
		}
	}

	private function getDB()
	{
		if ( !self::$db )
		{
			self::$db = \system\DbHelper::getInstance( 'huanpeng' );
		}

		return self::$db;
	}
}