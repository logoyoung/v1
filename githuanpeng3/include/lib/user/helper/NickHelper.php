<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/8/15
 * Time: 14:18
 */

namespace lib\user\helper;


use system\DbHelper;


class NickHelper
{
//	private $nick;
	private $db;

	const CHECK_MODEL_MACHINE_PASS = 3;

	/**
	 * @param mixed $nick
	 */
//	public function setNick( $nick )
//	{
//		$this->nick = $nick;
//	}

	private function getDb()
	{
		if ( !$this->db )
		{
			$this->db = DbHelper::getInstance( 'huanpeng' );
		}

		return $this->db;
	}

	private function _isUserOccupied( $nick )
	{

		$data = [ 'nick' => $nick ];

		$sql = "select uid from userstatic WHERE nick =:nick";

		$row = $this->getDb()->query( $sql, $data );
		if ( isset( $row[0]['uid'] ) && intval( $row[0]['uid'] ) != 0 )
		{
			return true;
		}
		else
		{
			return false;
		}

	}

	private function _isReviewedOccupied( $nick )
	{
		$data = [ 'nick' => $nick, 'status' => self::CHECK_MODEL_MACHINE_PASS ];

		$sql = "select uid from admin_user_nick WHERE status=:status AND oldnick=:nick";

		//注意 这里没有做异常处理，所以需要上次对昵称进行相关检测 保证传入的昵称是正确的
		$row = $this->getDb()->query( $sql, $data );
		if ( isset( $row[0]['uid'] ) && intval( $row[0]['uid'] ) != 0 )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function isOccupied( $nick )
	{
		return $this->_isUserOccupied( $nick )
			|| $this->_isReviewedOccupied( $nick );
	}

	/**
	 * 加入人工昵称审核
	 *
	 * @param $uid
	 * @param $oldNick
	 * @param $newNick
	 * @param $from       修改昵称来源
	 * @param $checkModel 审核模式
	 *
	 * @return bool
	 */
	public function toReviewed( $uid, $oldNick, $newNick, $from, $checkModel )
	{
		$data = [
			'nick'        => $newNick,
			'oldnick'     => $oldNick,
			'from'        => $from,
			'check_model' => $checkModel,
			'uid'         => $uid,
			'nick_up' => $newNick,
			'oldnick_up' => $oldNick,
			'from_up' => $from,
			'check_model_up' => $checkModel

		];

		$sql = "insert into admin_user_nick (nick,oldnick,`from`,check_model,uid) 
				VALUE (:nick,:oldnick,:from,:check_model,:uid) 
				on duplicate key update 
					nick=:nick_up,oldnick=:oldnick_up,`from`=:from_up,check_model=:check_model_up";

		try
		{
			$res = $this->getDb()->execute( $sql, $data );

			if ( $res )
			{
				return true;
			}
			else
			{
				return false;
			}
		} catch ( Exception $exception )
		{
			//todo log
			return false;
		}

	}

	public function create( $nickCopy = '' )
	{
		$nick = 'hp' . md5( random( 10, 1 ) . microtime( true ) );
		$nick = substr( $nick, 0, 10 );

		if ( $this->isOccupied( $nick ) || ( $nickCopy != '' && $nick == $nickCopy ) )
		{
			return $this->create($nickCopy);
		}
		else
		{
			return $nick;
		}
	}
}