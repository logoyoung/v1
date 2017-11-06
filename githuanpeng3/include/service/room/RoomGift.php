<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/7/27
 * Time: 14:22
 */


namespace service\room;

//include "../../../include/init.php";
use lib\anchor\AnchorGift;
use lib\Gift;


//todo 1.缓存模式，2.全局礼物存储，3.获取模式，4.每种配制模式的缓存，5,缓存的更新机制
class RoomGift
{
	private $_uid;

	private $_giftInfo;

	/**
	 * @param mixed $uid
	 */
	public function setUid( $uid )
	{
		$this->_uid = $uid;
	}

	public function getSendGiftConfig()
	{
		$giftObj = new AnchorGift();
		$giftObj->setUid( $this->_uid );
		$conf   = $giftObj->getGiftConfig();
		$result = [];

		if ( is_array( $conf ) )
		{
			$giftIdList = array_column( $conf, 'gift_id' );
			$giftIdList = array_unique( $giftIdList, SORT_REGULAR );

			$giftInfo = $this->getGiftInfo( $giftIdList );
			foreach ( $conf as $order => $info )
			{
				$currentGiftId  = $info['gift_id'];
				$result[$order] = $giftInfo[$currentGiftId];
				if ( $result[$order]['type'] == Gift::SEND_TYPE_COIN )
				{
					$result[$order]['cost'] = $conf[$order]['num'] * $result[$order]['money'];
					$result[$order]['num']  = $conf[$order]['num'];
					$result[$order]['unit'] = '欢朋币';

				}
				else
				{
					$result[$order]['cost'] = $conf[$order]['num'];
					$result[$order]['num']  = $conf[$order]['num'];
					$result[$order]['unit'] = '个';
				}


			}
		}

		$this->_rebuildData($result);

		return $result;
	}

	public function getGiftInfo( array $giftIds, $useRedis = false )
	{
		$result = [];

		if ( empty( $giftIds ) || !is_array( $giftIds ) )
		{
			return $result;
		}

		if ( !$useRedis )
		{
			$tmpIdList = [];

			foreach ( $giftIds as $giftId )
			{
				if ( isset( $this->_giftInfo[$giftId] ) )
				{
					$result[$giftId] = $this->_giftInfo[$giftId];
				}
				else
				{
					array_push( $tmpIdList, $giftId );
				}
			}

			if ( !empty( $tmpIdList ) )
			{
				$giftInfos = \lib\Gift::getGiftsInfo( $tmpIdList, \lib\Gift::getDB() );

				foreach ( $giftInfos as $giftId => $giftInfo )
				{
					$this->_giftInfo[$giftId] = $giftInfo;
					$result[$giftId]          = $this->_giftInfo[$giftId];
				}
			}
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
}

//$roomGift = new RoomGift();


//$roomGift->setUid( 1860 );

//var_dump( $roomGift->getSendGiftConfig() );