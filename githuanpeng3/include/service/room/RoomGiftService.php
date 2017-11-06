<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/9/18
 * Time: 上午10:25
 */

namespace service\room;


use service\gift\GiftService;
use service\room\helper\RoomGiftRedis;
use lib\room\RoomGift;
use lib\Gift;

class RoomGiftService
{
	public static $giftService;
	public static $giftInfo;

	public function getGiftCacheHandler():GiftService
	{
		if ( !static::$giftService )
		{
			static::$giftService = new GiftService();
		}

		return static::$giftService;
	}

	public function getCacheHandler():RoomGiftRedis
	{
		return RoomGiftRedis::getInstance();
	}

	public function getDbHandler():RoomGift
	{
		return RoomGift::getInstance();
	}

	public function getConfigIdList()
	{
		//todo get List from  db and redis;
		return [ -1, 1 ];
	}

	public function getRoomConfigId( $luid )
	{
		return 1;
	}

	public function getRoomMasterConfigId()
	{
		return RoomGiftRedis::ROOM_GIFT_MASTER_CONFIG_ID;
	}

	public function getRoomConfigMark( $configId, $fromdb = false )
	{
		$mark = null;

		if ( !$fromdb )
		{
			$mark = $this->getCacheHandler()->getConfigMark( $configId );
		}
		//当前缓存不存在 启用DB
		if ( is_null( $mark ) || $mark === false )
		{
			$detail = $this->getRoomConfigInfoFromDb( $configId );
			$this->getCacheHandler()->setConfigInfo( $configId, $detail );

			$mark   = $this->_buildRoomConfigMark( $detail );
			$update = $this->getCacheHandler()->setConfigMark( $configId, $mark );
			if ( !$update )
			{
				$this->_log( "update configid:$configId, mark=>$mark failed===fromdb=" . intval( $fromdb ) );
			}
		}
		else
		{

		}

		return $mark;
	}

	public function getGiftInfo( $giftids = null )
	{
		if ( !static::$giftInfo )
		{
//			$stime            = microtime( true );
			static::$giftInfo = $this->getGiftCacheHandler()->getGiftList();
//			$etime            = microtime( true );

//			$runtime = $etime - $stime;
//			echo __FUNCTION__ . " call time is " . $runtime . "\n";

			if ( !is_array( static::$giftInfo ) )
			{
				//todo log the static$giftinfo get failed;
				return [];
			}
		}

		$giftInfo = [];

		if ( is_null( $giftids ) )
		{
			return static::$giftInfo;
		}

		foreach ( $giftids as $giftid )
		{
			$giftInfo[$giftid] = static::$giftInfo[$giftid];
		}

		return $giftInfo;
	}

	public function getRoomConfigInfo( $configId )
	{
		$detail = $this->getCacheHandler()->getConfigInfo( $configId );
		if ( is_null( $detail ) || $detail === false || !is_array( $detail ) || empty( $detail ) )
		{
			$detail = $this->getRoomConfigInfoFromDb( $configId );

			$this->getCacheHandler()->setConfigInfo( $configId, $detail );
		}

		return $detail;
	}

	public function getRoomConfigInfoFromDb( $configId )
	{
		if ( $configId == RoomGiftRedis::ROOM_GIFT_MASTER_CONFIG_ID )
		{
			$detail = $this->_getRoomMasterConfigInfo();

		}
		else
		{
			$detail = $this->getDbHandler()->getRoomConfigInfo( $configId );
		}

		return $detail;
	}

	public function getRoomConfigGiftInfo( $configId )
	{
		$detail = $this->getRoomConfigInfo( $configId );
		$result = $this->_getRoomConfigGiftInfoByConfigInfo( $detail );


		return $result;
	}

	public function update( $configList = null )
	{
		if ( is_null( $configList ) )
		{
			$configList = $this->getConfigIdList();
		}

		$this->_log("configid list :".json_encode($configList));

		if ( is_array( $configList ) )
		{
			foreach ( $configList as $configId )
			{
				$this->getRoomConfigMark( $configId, true );
			}
		}
		elseif ( is_int( $configList ) )
		{
			$this->getRoomConfigMark( $configList, true );
		}


		return true;

//		$list = $this->getConfigIdList();
//		foreach ( $list as $configid )
//		{
//			$this->getRoomConfigMark( $configid, true );
//		}
//
//		return true;
	}

	public function delConfigItemFromDb( $id, $configId )
	{
		return $this->getDbHandler()->delConfigItem( $id, $configId );
	}

	public function addConfigItemFromDb( $configId, $giftId, $num, $order )
	{
		return $this->getDbHandler()->addConfigItem( $configId, $giftId, $num, $order );
	}

	public function updateConfigItemOrderFromDb( $id, $configid, $order )
	{
		return $this->getDbHandler()->updateConfigItemOrder( $id, $configid, $order );
	}

	private function _getRoomConfigGiftInfoByConfigInfo( $detail )
	{
		$result = [];
		$this->_formatData( $detail );

		if ( is_array( $detail ) )
		{
			$giftIds  = array_column( $detail, "gift_id" );
			$giftInfo = $this->getGiftInfo( $giftIds );
			foreach ( $detail as $order => $info )
			{
//				var_dump($info);

				$currentGiftId  = $info['gift_id'];
				$result[$order] = $giftInfo[$currentGiftId];
				if ( $result[$order]['type'] == Gift::SEND_TYPE_COIN )
				{
					$result[$order]['cost']      = $info['num'] * $result[$order]['money'];
					$result[$order]['num']       = $info['num'];
					$result[$order]['unit']      = '欢朋币';
					$result[$order]['sortOrder'] = $order;
					$result[$order]['item_id']   = $info['id'] ?? 0;
				}
				else
				{
					$result[$order]['cost']      = $info['num'];
					$result[$order]['num']       = $info['num'];
					$result[$order]['unit']      = '个';
					$result[$order]['sortOrder'] = $order;
					$result[$order]['item_id']   = $info['id'] ?? 0;
				}
			}
		}

		$this->_rebuildData( $result );

		return $result;
	}

	private function _getRoomMasterConfigInfo()
	{
//		$stime    = microtime( true );
		$giftInfo = $this->getGiftInfo();
//		$etime    = microtime( true );
//		$runtime  = $etime - $stime;
//		echo __FUNCTION__ . "call time is " . $runtime . "\n";
		$result = [];
		$i      = 0;
		foreach ( $giftInfo as $giftid => $info )
		{
			$tmp['gift_id'] = $giftid;
			$tmp['order']   = $i;
			if ( $info['type'] == Gift::SEND_TYPE_COIN )
			{

				$tmp['num'] = 1;
			}
			else
			{
				$tmp['num'] = 100;
			}

			$i++;
			array_push( $result, $tmp );
		}

		return $result;
	}

	private function _rebuildData( &$result )
	{
		$conf  = $GLOBALS['env-def'][$GLOBALS['env']];
		$field = [ 'bg', 'bg_3x', 'poster', 'poster_3x', 'web_preview', 'web_bg', 'thumb_poster', 'thumb_poster_3x' ];

		foreach ( $result as $index => $data )
		{
			foreach ( $field as $item )
			{
				if ( $data[$item] )
				{
					$result[$index][$item] = "http://" . $conf['domain-img'] . $data[$item];
				}
			}

			$result[$index]['mark'] = md5( json_encode( $result[$index] ) );
		}
	}

	private function _formatData( &$detail )
	{
		foreach ( $detail as $key => $info )
		{
			if ( isset( $info['giftid'] ) )
			{
				$detail[$key]['gift_id'] = $info['giftid'];
				unset( $detail[$key]['giftid'] );
			}
		}
	}

	private function _buildRoomConfigMark( $detail )
	{
		$this->_log(json_encode($detail)."online ==>".__LINE__);

		$giftInfo   = $this->_getRoomConfigGiftInfoByConfigInfo( $detail );
		$this->_log(json_encode($giftInfo));

		$array_mark = array_column( $giftInfo, 'mark' );
		$this->_log(json_encode($array_mark)."online ==>".__LINE__);
		$str = implode( "", $array_mark );
		$this->_log( "mark pre is $str" );

		$mark = md5( $str );
		$this->_log( "mark result is $mark" );

		return $mark;
	}

	private function _log( $msg )
	{
		$logname = "RoomGiftService";
		write_log( $msg, $logname );
	}
}