<?php

/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/7/12
 * Time: 10:02
 */

include __DIR__ . "/../../include/init.php";

class UserNick
{

	private static $_db;
	private        $_uid;

	private static $_userObjArray = [];

	public function checkNickIsExist( $nick )
	{
		return $this->_checkNickIsExistInUserStatic( $nick ) || $this->_checkNickIsExistInAdmin( $nick );
	}

	private function _checkNickIsExistInUserStatic( $nick )
	{
		$sql = "select uid from userstatic where nick='$nick'";
		$res = $this->getDB()->query( $sql );

		if ( !$res )
		{
			//todo log
			throw new Exception();
		}

		$row = $res->fetch_assoc();

		if ( $row['uid'] )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private function _checkNickIsExistInAdmin( $nick )
	{
		$sql = "select uid from admin_user_nick WHERE status=3 AND oldnick='$nick'";
		$res = $this->getDB()->query( $sql );

		if ( !$res )
		{
			//todo log
			throw new Exception();
		}

		$row = $res->fetch_assoc();

		if ( $row['uid'] )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	private function _addToAdminModifyNick( $oldNick, $newNick, $from, $checkModel )
	{
		$insertData = $updateData = [
			'nick'        => $newNick,
			'oldnick'     => $oldNick,
			'from'        => $from,
			'check_model' => $checkModel
		];

		$insertData['uid'] = $this->_uid;

		$sql = $this->getDB()->insertDuplicate( 'admin_user_nick', $insertData, $updateData, true );

		$res = $this->getDB()->query( $sql );
		if ( !$res )
		{
			//todo log
//			throw new Exception();

			return false;
		}

//		return $this->getDB()->affectedRows;
		return true;
	}

	private function _modifyUserNick( $oldNick, $newNick, $from )
	{


		$checkModel = checkMode( CHECK_NICK, $this->getDB() );
		if ( !$this->_addToAdminModifyNick( $oldNick, $newNick, $from, $checkModel ) )
		{
			return false;
		}

		if ( $checkModel )
		{
			//先发
			$user = new lib\User( $this->_uid );

			return $user->updateNick( $newNick );
		}

		return true;
	}

	public function registerAddNick($nick)
	{
		$oldNick = $this->createNick();
		$newNick = $nick;
		$from = self::NICK_FROM_REGISTER;
		return $this->_modifyUserNick($oldNick,$newNick,$from);
	}

	public function threeSideAddNick($nick)
	{
		$oldNick = $this->createNick();
		$newNick = $nick;
		if($this->checkNickIsExist($nick))
		{
			$newNick = $this->createNick($oldNick);
		}

		$from = self::NICK_FROM_THREE;

		return $this->_modifyUserNick($oldNick,$newNick,$from);
	}

	public function modifyNick($nick)
	{
		$userInfo = $this->getUserObj()->getUserInfo();
		if(!$userInfo)
		{
			return false;
		}

		$oldNick = $userInfo['nick'];
		$newNick = $nick;
		$from = self::NICK_FROM_MODIFY;

		return $this->_modifyUserNick($oldNick,$newNick,$from);
	}

	public function createNick($nickCopy='')
	{
		$nick = 'hp' . md5( random( 10, 1 ) . microtime( true ) );
		$nick = substr( $nick, 0, 10 );

		if ( $this->checkNickIsExist( $nick ) || ($nickCopy != '' && $nick == $nickCopy) )
		{
			return $this->createNick();
		}
		else
		{
			return $nick;
		}
	}

	/**
	 * @param mixed $uid
	 */
	public function setUid( $uid )
	{
		$this->_uid = $uid;
	}

	/**
	 * @param mixed $db
	 */
	public function setDb( $db )
	{
		self::$_db = $db;
	}

	private function getDB(): DBHelperi_huanpeng
	{
		if ( !self::$_db )
		{
			self::$_db = new DBHelperi_huanpeng();
		}

		return self::$_db;
	}

	/**
	 * @return array
	 */
	private function getUserObj():lib\User
	{
		if ( !isset( self::$_userObjArray[$this->_uid] ) )
		{
			self::$_userObjArray[$this->_uid] = new lib\User($this->_uid,$this->getDB());
		}

		return self::$_userObjArray[$this->_uid];
	}
}