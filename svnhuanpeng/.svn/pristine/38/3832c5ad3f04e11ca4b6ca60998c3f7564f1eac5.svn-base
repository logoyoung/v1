<?php
/**
 * Created by PhpStorm.
 * User: SEELE
 * Date: 2017/5/2
 * Time: 13:45
 */

namespace lib;

//use lib\FinanceBase;

/**
 *
 *
 * Class FinanceStatistic
 *
 * @package hp\lib
 */
class FinanceStatistic extends FinanceBase
{
	const REDIS_KEY_MONTH_INCOME = "HASH_MONTH_INCOME";
	const REDIS_KEY_DAY_INCOME   = "HASH_DAY_INCOME";
	const REDIS_KEY_DAY_PURCHASE = "HASH_DAY_PURCHASE";

	protected $_db;
	protected $_redis;
	private   $_redisKeyConfig = [];

	public function __construct( \DBHelperi_huanpeng $db = null, \RedisHelp $redis = null )
	{
		parent::__construct();
		if ( $redis )
		{
			$this->_redis = $redis;
		}
		else
		{
			$this->_redis = new \RedisHelp();
		}

		if ( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new \DBHelperi_huanpeng();
		}

	}

	/**
	 * 根据开始时间以及结束时间获取充值总量
	 *
	 * @param int $stime
	 * @param int $etime
	 *
	 * @return mixed
	 */
	public function getTotalRecharge( int $stime, int $etime )
	{
		$dateList = $this->_getSearchDateList( $stime, $etime );
		$result   = [];
		foreach ( $dateList as $date )
		{
			$tmp = $this->_getTotalRechargeOnOneMonth( $date[0], $date[1] );
			array_push( $result, $tmp );
		}

		$res = array_reduce( $result, function ( $res, $item )
		{
			if ( !$res )
			{
				return $item;
			}
			foreach ( $res as $key => $value )
			{
				$item[$key] += $value;
			}

			return $item;
		}, [] );

		return $res;
	}


	/**
	 * 获取每月充值总量
	 *
	 * @param int $date
	 *
	 * @return array|bool 成功返回 成功返回 ['alipay'=>33100,weichat=>'1123123']
	 */
	public function getTotalRechargeByMonth( int $date )
	{
		$date   = $this->_getOneMonthDate( $date );
		$result = $this->_getTotalRechargeOnOneMonth( $date[0], $date[1] );
		foreach ( $result as $k => $v )
		{
			$result[$k] = $this->getOutputNumber( $v );
		}

		return $result;
	}

	/**
	 * 获取一个月时间内的充值总额
	 *
	 * @param string $stime
	 * @param strtig $etime
	 *
	 * @return array|bool 成功返回 ['alipay'=>33100,weichat=>'1123123']
	 */
	private function _getTotalRechargeOnOneMonth( string $stime, string $etime )
	{
		$table = $this->_getTableList( strtotime( $stime ) );

		$sql = "select sum(rmb) as rmb,channel from {$table['recharge']} where status=" . self::RECHARGE_STATUS_FINISH . " and ctime BETWEEN '$stime' and '$etime' group by channel";
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			return false;
		}

		$list = array();
		while ( $row = $res->fetch_assoc() )
		{
			$list[$row['channel']] = $row['rmb'];
		}

		return $list;
	}

	/**
	 * 获取每月余额
	 *    可以用户获取每月收益
	 *
	 * @param int $date
	 *
	 * @return array
	 */
	public function getMonthBalance( int $date )
	{
		$date = $this->_getOneMonthDate( $date );

		$result = [ 'gd' => 0, 'gb' => 0, 'hb' => 0, 'hd' => 0 ];

		foreach ( $this->_getOneMonthBalanceListByYield( $date[0] ) as $value )
		{
			$result['gd'] += $value['gd'];
			$result['gb'] += $value['gb'];
			$result['hb'] += $value['hb'];
			$result['hd'] += $value['hd'];
		}

		foreach ( $result as $key => $value )
		{
			$result[$key] = $this->getOutputNumber( $value );
		}

		return $result;
	}


	public function getMonthAnchorBalanceList( int $date )
	{
		$date = $this->_getOneMonthDate( $date );

		$result = [];

		foreach ( $this->_getOneMonthBalanceListByYield( $date[0] ) as $value )
		{
			if ( $value['gd'] || $value['gb'] )
			{
				$tmp['uid'] = $value['uid'];
				$tmp['gb']  = $this->getOutputNumber( $value['gb'] );
				$tmp['gd']  = $this->getOutputNumber( $value['gd'] );

				array_push( $result, $tmp );
			}
		}

		return $result;
	}

	private function _getOneMonthBalanceListByYield( string $stime )
	{
		$table = $this->_getTableList( strtotime( $stime ) );

		$sqlTab = "select * from {$table['statement']} ORDER BY id DESC";
		$sql    = "select id, uid,gb,hb,gd,hd from ($sqlTab) as tmp GROUP BY uid";

		$res = $this->_db->query( $sql );

		while ( $row = $res->fetch_assoc() )
		{
			yield $row;
		}
	}

	public function getMonthInnerRechargeTotal( $stime, $etime )
	{
		$table = $this->_getTableList( strtotime( $stime ) );
		//todo 修改
		$sql = "select sum(hd) as hd,sum(hb) as hb,sum(gb) as gb, sum(gd) as gd from {$table['innerRecharge']} where channel != 0 and ctime between '$stime' and '$etime'";
		$res = $this->_db->query( $sql );
		$row = $res->fethc_assoc();

		return $row;
	}

	public function getMonthInnerRechargeList( int $date )
	{
		$table  = $this->_getTableList( $date );
		$result = [];
		$sql    = "select * from {$table['innerRecharge']} where channel != 0";
		$res    = $this->_db->query( $sql );


		while ( $row = $res->fetch_assoc() )
		{
			$tmp['uid']     = $row['uid'];
			$tmp['hb']      = $this->getOutputNumber( $row['hb'] );
			$tmp['hd']      = $this->getOutputNumber( $row['hd'] );
			$tmp['gd']      = $this->getOutputNumber( $row['gd'] );
			$tmp['gb']      = $this->getOutputNumber( $row['gb'] );
			$tmp['channel'] = $row['channel'];
			$tmp['otid']    = $row['otid'];
			array_push( $result, $tmp );
		}

		return $result;
	}

	/**
	 * 获取个人本日收益
	 *
	 * @param int $date
	 * @param int $uid
	 *
	 * @return bool|array  ['gb'=>123123,'gd'=>456456];
	 */
	public function getUserDayIncome( int $date, int $uid )
	{
		$stime = date("Y-m-d", $date)." 00:00:00";
		$etime = date("Y-m-d", $date)." 23:59:59";

		$type = self::REDIS_KEY_DAY_INCOME;

		$lastValue = $this->_getIncomeByRedis( $date, $uid, $type );

		$result['gb'] = $lastValue['coin'];
		$result['gd'] = $lastValue['bean'];

		$table = $this->_getTableList( $date );
		$sql   = "select max(id) as id,sum(gbd) as gb,sum(gdd) as gd from {$table['statement']} where uid=$uid  
					and id>{$lastValue['lastId']} and (`type`=" . self::STATEMENT_TYPE_SENDGIFT . " or `type`=" . self::STATEMENT_TYPE_SENDBEAN . ")"." and ctime between '$stime' and '$etime'";
		$res   = $this->_db->query( $sql );
		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "($date, $uid)[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			return false;
		}

		$row = $res->fetch_assoc();
		$row['id'] = intval($row['id']);

		//lastId 以后 没有送礼记录
		if ( $lastValue['lastId'] >= $row['id'] )
		{
//			return $result;
		}else{
			$result['gb'] += $row['gb'];
			$result['gd'] += $row['gd'];

			$this->_setIncomeByRedis( $date, $type, $uid, $row['id'], $result['gb'], $result['gd'] );
		}

		foreach ($result as $key  => $value)
		{
			$result[$key] = $this->getOutputNumber($value);
		}

		return $result;
	}

	/**
	 * 获取个人本月收益
	 *
	 * @param int $date
	 * @param int $uid
	 *
	 * @return bool|array  ['gb'=>123123,'gd'=>456456];
	 */
	public function getUserMonthIncome( int $date, int $uid )
	{

		$type = self::REDIS_KEY_MONTH_INCOME;

		$lastValue = $this->_getIncomeByRedis( $date, $uid, $type );

		$result['gb'] = $lastValue['coin'];
		$result['gd'] = $lastValue['bean'];

		$table = $this->_getTableList( $date );
		$sql   = "select max(id) as id,sum(gbd) as gb,sum(gdd) as gd from {$table['statement']} where uid=$uid  
					and id>{$lastValue['lastId']} and (`type`=" . self::STATEMENT_TYPE_SENDGIFT . " or `type`=" . self::STATEMENT_TYPE_SENDBEAN . ")";
		$res   = $this->_db->query( $sql );
		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "($date, $uid)[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			return false;
		}

		$row = $res->fetch_assoc();
		$row['id'] = intval($row['id']);


		//lastId 以后 没有送礼记录
		if ( $lastValue['lastId'] >= $row['id'] )
		{
//			return $result;
		}else{
			$result['gb'] += $row['gb'];
			$result['gd'] += $row['gd'];

			$this->_setIncomeByRedis( $date, $type, $uid, $row['id'], $result['gb'], $result['gd'] );
		}

		foreach ($result as $key  => $value)
		{
			$result[$key] = $this->getOutputNumber($value);
		}

		return $result;
	}
	public function getUserDaySendPurchase($date,$uid)
	{
		$stime = date("Y-m-d", $date)." 00:00:00";
		$etime = date("Y-m-d", $date)." 23:59:59";

		$type = self::REDIS_KEY_DAY_PURCHASE;
		$lastValue = $this->_getIncomeByRedis($date,$uid,$type);

		$result['hb'] = $lastValue['coin'];
		$result['hd'] = $lastValue['bean'];

		$table = $this->_getTableList( $date );

		$sql = "select max(id) as id , sum(hbd) as hb, sum(hdd) as hd from {$table['statement']} where uid=$uid 
			  and id>{$lastValue['lastId']} and (`type`=" . self::STATEMENT_TYPE_SENDGIFT . " or `type`=" . self::STATEMENT_TYPE_SENDBEAN . ")"." and ctime between '$stime' and '$etime'";

		$res   = $this->_db->query( $sql );
		if ( !$res )
		{
			$t = "QueryError @ " . __CLASS__ . "::" . __FUNCTION__ . "($date, $uid)[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			return false;
		}
//		echo $sql."\n";

		$row = $res->fetch_assoc();
		$row['id'] = intval($row['id']);


		//lastId 以后 没有送礼记录
		if ( $lastValue['lastId'] >= $row['id'] )
		{
//			return $result;
		}else{
			$result['hb'] += abs($row['hb']);
			$result['hd'] += abs($row['hd']);

			$this->_setIncomeByRedis( $date, $type, $uid, $row['id'], $result['hb'], $result['hd'] );
		}

		foreach ($result as $key  => $value)
		{
			$result[$key] = $this->getOutputNumber($value);
		}

		return $result;
	}

	private function _getIncomeByRedis( int $date, int $uid, $type )
	{
		$this->_redisKeyConfig["k".date("Ymd",$date)][self::REDIS_KEY_DAY_INCOME] = [
			'key'          => self::REDIS_KEY_DAY_INCOME . "_" . date( 'Ymd', $date ),
			'defaultData'  => json_encode( [ 'coin' => 0, 'bean' => 0, 'lastId' => 0 ] ),
			'defaultField' => $uid,
			'expire'       => 24 * 3600
		];

		$this->_redisKeyConfig["k".date("Ymd",$date)][self::REDIS_KEY_MONTH_INCOME] = [
			'key'          => self::REDIS_KEY_MONTH_INCOME . "_" . date( 'Ym', $date ),
			'defaultData'  => json_encode( [ 'coin' => 0, 'bean' => 0, 'lastId' => 0 ] ),
			'defaultField' => $uid,
			'expire'       => 600//( date( 't', $date ) - date( 'j', $date ) + 1 ) * ( 24 * 3600 )
		];

		$this->_redisKeyConfig["k".date("Ymd",$date)][self::REDIS_KEY_DAY_PURCHASE] = [
			'key'          => self::REDIS_KEY_DAY_PURCHASE . "_" . date( 'Ymd', $date ),
			'defaultData'  => json_encode( [ 'coin' => 0, 'bean' => 0, 'lastId' => 0 ] ),
			'defaultField' => $uid,
			'expire'       => 24*3600
		];

		foreach ( $this->_redisKeyConfig["k".date("Ymd",$date)] as $key => $val )
		{
			if($key == $type)
			{
				$rKey   = $val['key'];
				$rField = $val['defaultField'];
				$rValue = $val['defaultData'];

				if ( !$this->_redis->isExists( $val['key'] ) )
				{
					$this->_redis->hset( $rKey, $rField, $rValue );
					$this->_redis->expire( $rKey, $val['expire'] );

					return json_decode( $rValue, true );
				}

				if ( !$this->_redis->getMyRedis()->hExists( $rKey, $rField ) )
				{
					$this->_redis->hset( $rKey, $rField, $rValue );

					return json_decode( $rValue, true );
				}

				$rValue = $this->_redis->hget( $rKey, $rField );
				$rValue = json_decode( $rValue , true);

				if ( !$rValue || !isset( $rValue['coin'] ) || !isset( $rValue['bean'] ) || !isset( $rValue['lastId'] ) )
				{
					$this->_redis->hdel( $rKey, $rField );

					return $this->_getIncomeByRedis( $date, $uid );
				}

				return $rValue;
			}
		}
	}

	private function _setIncomeByRedis( $date, $type, $uid, $lastId, $gb, $gd )
	{
		$key   = $this->_redisKeyConfig["k".date("Ymd",$date)][$type]['key'];
		$field = $uid;
		$val   = json_encode( [ 'lastId' => $lastId, 'coin' => $gb, 'bean' => $gd ] );

		$this->_redis->hset( $key, $field, $val );

		return true;
	}


	private function _getSearchDateList( int $stime, int $etime )
	{
		if ( $stime > $etime )
		{
			return [];
		}

		$sdate = date( "Y-m-d H:i:s", $stime );
		$edate = date( 'Y-m-d H:i:s', $etime );

		$start = explode( '-', $sdate );
		$end   = explode( '-', $edate );

		$betweenYear  = $end[0] - $start[0];
		$betweenMonth = $betweenYear + $end[1] - $start[1];

		$list = [];

		if ( $betweenMonth <= 0 )
		{
			array_push( $list, [ $sdate, $edate ] );
		}
		else
		{
			$endDate = $this->_getOneMonthDateEnd();
			array_push( $list, [ $sdate, $endDate ] );

			for ( $i = 1; $i < $betweenMonth; $i++ )
			{
				$stime     = strtotime( "+1 month", $stime );
				$date      = $this->_getOneMonthDate( $stime );
				$startDate = $date[0];
				$endDate   = $date[1];

				array_push( $list, [ $startDate, $endDate ] );
			}

			$startDate = $this->_getOneMonthDateStart( $etime );
			array_push( $list, [ $startDate, $edate ] );
		}

		return $list;
	}

	private function _getOneMonthDate( int $timestamp )
	{
		$sdate = $this->_getOneMonthDateStart( $timestamp );
		$edate = $this->_getOneMonthDateEnd( $timestamp );

		return [ $sdate, $edate ];
	}

	private function _getOneMonthDateStart( int $timestamp = 0 )
	{
		if ( !$timestamp )
		{
			$timestamp = time();
		}

		return date( "Y-m", $timestamp ) . "-01 00:00:00";
	}

	private function _getOneMonthDateEnd( int $timestamp = 0 )
	{
		if ( !$timestamp )
		{
			$timestamp = time();
		}

		return date( "Y-m-t", $timestamp ) . " 23:59:59";

	}

}