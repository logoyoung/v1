<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/3/17
 * Time: 下午3:02
 */


class Statement
{
	const STATEMENT_LOCK_KEY = "hpf_statement:";
	const STATEMENT_LOCKED = 1;
	const STATEMENT_UNLOCKED = 0;
	const TABLE_STATEMENT_PRE = "hpf_statement_";

	//流水表类型
	const STATEMENT_TYPE_SEND = 1;//送礼
	const STATEMENT_TYPE_RECHARGE = 2; //充值
	const STATEMENT_TYPE_EXCHANGE = 3; //兑换
	const STATEMENT_TYPE_GD_GB = 4; //金豆=>金币
	const STATEMENT_TYPE_WITHDRAW = 5;//提现
	const STATEMENT_TYPE_INTERNAL_RECHARGE = 6;//内部发放
	const STATEMENT_TYPE_RENAME = 7;//改名

	private $dbObj = null;
	private $redisObj = null;
	private $table = "";

	public function init($db, $redis, $type)
	{
		if( !$db )
			$this->dbObj = new DBHelperi_huanpeng();
		else
			$this->dbObj = $db;

		if( !$redis )
			$this->redisObj = new redishelp();
		else
			$this->redisObj = $redis;

		$this->table['statement'] = self::TABLE_STATEMENT_PRE.date("Ym");
//		$this->table['rate'] = self::

		return $this;
	}

	protected function _statement( $uid, $hbd, $gbd, $tid, $type )
	{
		$result = true;
		if( $this->_getStatementLocketStatus( $uid ) )
		{
			$result = false;
		}

		if($result)
		{
			$this->_lockStatement( $uid );

			$balance = $this->getBalance($uid);
			$hb = $balance['hb'];
			$gb = $balance['gb'];

			if( ($hbd < 0 && $hb < $hbd) || ( $gbd < 0 && $gb < $gbd ) )
			{
				$result = false;
			}
		}

		if($result)
		{
			$hb = $hb + $hbd;
			$gb = $gb + $gbd;

			$sql = "insert into {$this->tabList['statement']} (`uid`, `hb`, `gb`, `hbd`, `gbd`, `tid`, `type`)".
				" value($uid, $hb, $gb, $hbd, $gbd, '$tid', $type)";
			$result = $this->db->query( $sql );
		}

		$this->_unlockStatement( $uid );

		return $result;
	}

	private function _lockStatement( $uid )
	{
		$key = self::STATEMENT_LOCK_KEY . "$uid";
		$this->redis->set($key, self::STATEMENT_LOCKED);
	}

	private function _unlockStatement( $uid )
	{
		$key = self::STATEMENT_LOCK_KEY . "$uid";
		$this->redis->del($key);
	}

	private function _getStatementLocketStatus( $uid )
	{
		$key = self::STATEMENT_LOCK_KEY . "$uid";
		return (int)$this->redis->get($key);
	}
}