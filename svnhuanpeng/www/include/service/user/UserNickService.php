<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/16
 * Time: 14:47
 */

namespace service\user;


use lib\User;
use lib\user\helper\NickHelper;
use service\rule\TextService;
use tool\ValidationTool;

class UserNickService
{
	const NICK_FROM_REGISTER = 1;
	const NICK_FROM_THREE    = 2;
	const NICK_FROM_MODIFY   = 3;

	private $uid;
	private $userObjArray = [];
	private $nickHelper;


	/**
	 * @param mixed $uid
	 */
	public function setUid( $uid )
	{
		$this->uid = $uid;
	}

	/**
	 * @return NickHelper
	 */
	public function getNickHelper(): NickHelper
	{
		if ( !$this->nickHelper )
		{
			$this->nickHelper = new NickHelper();
		}

		return $this->nickHelper;
	}

	private function getUserObj(): User
	{
		if ( !isset( $this->userObjArray[$this->uid] ) )
		{
			$this->userObjArray[$this->uid] = new User( $this->uid );
		}

		return $this->userObjArray[$this->uid];
	}

	private function _addOrUpdate( $oldNick, $newNick, $from )
	{
		$checkModel = checkMode( CHECK_NICK, new \DBHelperi_huanpeng() );

		$result = $this->getNickHelper()->toReviewed( $this->uid, $oldNick, $newNick, $from, $checkModel );
		if ( !$result )
		{
			return false;
		}

		//先发后审
		if ( $checkModel )
		{
			$result = $this->getUserObj()->updateNick( $newNick );

			return $result ? true : false;
		}

		return true;
	}

	public function alterByRegister( $nick )
	{
		$oldNick = $this->createNick();
		$newNick = $nick;
		$from    = self::NICK_FROM_REGISTER;

		return $this->_addOrUpdate( $oldNick, $newNick, $from );
	}

	public function alterByThreeSideLogin( $nick )
	{
		$oldNick = $this->createNick();
		$newNick = $nick;
		$from    = self::NICK_FROM_THREE;

		if ( $this->isNickWasOccupied( $newNick ) )
		{
			$newNick = $this->createNick( $oldNick );
		}

		return $this->_addOrUpdate( $oldNick, $newNick, $from );
	}

	public function alterByModifyNick( $oldNick, $newNick )
	{

		$from = self::NICK_FROM_MODIFY;

		return $this->_addOrUpdate( $oldNick, $newNick, $from );
	}

	public function createNick( $nickCopy = '' )
	{
		return $this->getNickHelper()->create( $nickCopy );
	}

	public function isNickWasOccupied( $nick )
	{
		return $this->getNickHelper()->isOccupied( $nick );
	}

	/**
	 * 检测昵称是否有效
	 *    没有匹配昵称是否已经存在
	 *
	 * @param        $nick
	 * @param int    $errno
	 * @param string $desc
	 *
	 * @return bool
	 */
	public static function isValidNick( &$nick, &$errno = 0, &$desc = '' )
	{
		$nick = ValidationTool::filterWords( $nick );

		if ( !$nick )
		{
			$errno = -4064;
			$desc  = errorDesc( $errno );

			return false;
		}

		$textService = new TextService();
		$textService->setCaller( 'api:' . __FILE__ . ';line:' . __LINE__ );

		$port = '';
		$textService->addText( $nick, time(), TextService::CHANNEL_NICKNAME )->setIp( fetch_real_ip( $port ) );

		if ( !$textService->checkStatus() )
		{
			$errno = -4091;
			$desc  = errorDesc( $errno );

			return false;
		}

		if ( !ValidationTool::checkEmoji( $nick ) )
		{
			$errno = -4091;
			$desc  = errorDesc( $errno );

			return false;
		}

		if ( !ValidationTool::checkNickLength( $nick ) )
		{
			$errno = -4010;
			$desc  = errorDesc( $errno );

			return false;
		}

		return true;
	}
}