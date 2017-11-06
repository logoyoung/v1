<?php
namespace service\chatRoom;
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/9/13
 * Time: 下午5:06
 */
class ChatRoomService
{
	private $uid;

	private $dao;

	 /**
	 * @param mixed $uid
	 */
	public function setUid( $uid )
	{
		$this->uid = $uid;
	}

	public function getChatRoomIdByUid( $getAll = false )
	{
		$this->DBService()->setUid($this->uid);

		return $this->DBService()->getChatRoomIdByUid($getAll);
	}

	private function DBService()
	{
		if(!$this->dao)
		{
			$this->dao = new \lib\chatRoom\ChatRoom();
		}

		return $this->dao;
	}
}