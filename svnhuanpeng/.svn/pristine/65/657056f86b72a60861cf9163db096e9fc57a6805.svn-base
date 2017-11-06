<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/6/30
 * Time: 16:41
 */

namespace service\due;
use lib\LivePush;
use lib\ApplePush;
use lib\MsgPackage;

class DueApplePush
{
	private $_db;
	private $_applePushObj;
	private $_livePushObj;
	private $_fromUid;
	private $_pic;
	private $_toDoNumber;
	private $_nick;

	private static $_codeToSendType = [
		10000=>10,
		10001 => 11,
		10002 => 12,
		10003 => 13,
		10004=>14
	];

	private static $_msgTitle = [
		10000 => '',
		10001 => '收到新订单',
		10002 => '用户退单',
		10003 => '主播接单了',
		10004 => '主播以拒单'
	];

	public function sendMsg($senderUid, $receiveUid,$msg,$code,$customData=[])
	{
		$list = false;

		if(is_numeric($receiveUid))
		{
			$list = [$receiveUid];
		}

		if(is_array($receiveUid))
		{
			$list = $receiveUid;
		}

		if(is_array($list) )
		{
			$deviceTokenList = $this->getLivePushObj()->getApplePushList($list);
			if($deviceTokenList)
			{
				foreach ($deviceTokenList as $token)
				{
					if(!$customData)
					{
						$customData = $this->_buildCustomData($code);
					}

					$mid = implode('-',[$senderUid,$receiveUid,$code,time()]);
					$msg = MsgPackage::getDueOrderApplePushMsgPackage($token['deviceToken'],$msg,$mid,$customData);

					return $this->_send($msg);
				}
			}
		}
	}

	private function _send($msg)
	{
		$result = $this->getPushObj()->send($msg);
		return $result;
// 		file_put_contents("/data/logs/yalong.log", $result);
	}

	public function getDb() :\DBHelperi_huanpeng
	{
		if(!$this->_db)
		{
			$this->_db = new \DBHelperi_huanpeng();
		}

		return $this->_db;
	}
	public function getPushObj(): ApplePush
	{
		if(!$this->_applePushObj)
		{
			$this->_applePushObj = new ApplePush();
		}

		return $this->_applePushObj;
	}

	public function getLivePushObj():LivePush
	{
		if(!$this->_livePushObj)
		{
			$this->_livePushObj = new LivePush($this->getDb());
		}

		return $this->_livePushObj;
	}


	/**
	 * @param mixed $pic
	 */
	public function setPic( $pic )
	{
		$this->_pic = $pic;
	}

	/**
	 * @param mixed $targetUid
	 */
	public function setFromUid( $fromUid )
	{
		$this->_fromUid = $fromUid;
	}

	/**
	 * @param mixed $toDoNumber
	 */
	public function setToDoNumber( $toDoNumber )
	{
		$this->_toDoNumber = $toDoNumber;
	}

	/**
	 * @param mixed $nick
	 */
	public function setNick( $nick )
	{
		$this->_nick = $nick;
	}

	private function _buildCustomData($code)
	{
		$data['type'] = static::$_codeToSendType[$code];
		$data['data'] = [
			'pic' => $this->_pic,
			'luid' => $this->_fromUid,
			'toDoNum' =>$this->_toDoNumber,
			'nick' => $this->_nick,
			'title' => static::$_msgTitle[$code]
		];

		return $data;
	}
}