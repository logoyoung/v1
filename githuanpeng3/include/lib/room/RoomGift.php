<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/9/18
 * Time: 上午11:09
 */

namespace lib\room;


class RoomGift
{
	private $_db;

	static $self;

	public function getDb()
	{
		if ( !$this->_db )
		{
			$this->_db = \system\DbHelper::getInstance( 'huanpeng' );
		}

		return $this->_db;
	}

	public static function getInstance():RoomGift
	{
		if ( !static::$self )
		{
			static::$self = new RoomGift();
		}

		return static::$self;

	}

	public function getRoomConfigInfo( $configId )
	{
		$data = [ 'config_id' => $configId ];
		$sql  = "select id, gift_id, `order`,num from gift_config_detail WHERE config_id=:config_id ORDER BY `order`";

		$result = $this->getDb()->query( $sql, $data );

		$detail = [];

		foreach ( $result as $value )
		{
			//todo 礼物ID以及数量作为主键 如果以后，不再存在同一礼物不同数量的情况，则可以改进
//			$key = $value['gift_id']."-".$value['num'];
//			$key          = $value['order'];
//			$detail[$key] = $value;
			array_push($detail,$value);
		}

		return $detail;
	}

	public function delConfigItem( $id, $configId )
	{
		$data = [ 'id' => $id, "config_id" =>$configId ];
		$sql  = "delete from gift_config_detail where id=:id AND config_id=:config_id";

		try
		{
			$result = $this->getDb()->execute( $sql, $data );
			if ( $result > 0 )
			{
				return true;
			}
			else
			{
				return false;
			}
		} catch ( \Exception $exception )
		{
			return false;
		}
	}

	public function addConfigItem( $configId, $giftId, $num, $order )
	{
		$data = [
			"config_id" => $configId,
			'gift_id' => $giftId,
			"num" => $num,
			"item_order" => $order
		];

		$sql  = "insert into gift_config_detail(config_id,gift_id,num,`order`) 
				VALUE(:config_id,:gift_id,:num,:item_order)";

		try
		{
			$result = $this->getDb()->execute($sql, $data);
			if($result > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		catch (\Exception $exception)
		{
			return false;
		}
	}

	public function updateConfigItemOrder( $id, $configId,$order )
	{
		$data = [
			"id" => $id,
			"config_id" => $configId,
			"item_order" => $order
		];

		$sql = "update gift_config_detail set `order`=:item_order where id=:id and config_id=:config_id";

		try
		{
			$result = $this->getDb()->execute($sql, $data);
			if($result > 0)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		catch (\Exception $exception)
		{
			return false;
		}
	}
}