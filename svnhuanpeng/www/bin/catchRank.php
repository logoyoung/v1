<?php
require '/usr/local/huanpeng/include/init.php';

class Rank
{

	private $_db = null;
	public $redis = null;

	const USER_TYPE_A = 1;    //用户类型 1为主播
	const USER_TYPE_U = 2;    //用户类型2为观众

	const TIME_TYPE_D = 1;    //时间排序 按日
	const TIME_TYPE_W = 2;    //时间排续 按周
	const TIME_TYPE_M = 3;    //时间排续 按月

	const ORDER_TYPE_1 = 1;    //排序 1为按收入(主播)或贡献财富(观众)
	const ORDER_TYPE_2 = 2;    //排序 2为按人气
	const ORDER_TYPE_3 = 3;    //排序 3等级

	const ANCHOR_SALARY = 'anchorSalary'; //主播收入榜前缀
	const ANCHOR_POPULARITY = 'anchorPopularity';//主播人气榜前缀
	const USER_DEVOTE = 'userDevote';//观众贡献榜前缀
	const ANCHOR_SALARY_WEEK = 'anchorSalaryWeek'; //主播收入周榜前缀
	const ANCHOR_POPULARITY_WEEK = 'anchorPopularityWeek';//主播人气周榜前缀
	const USER_DEVOTE_WEEK = 'userDevoteWeek';//观众贡献周榜前缀
	const ANCHOR_SALARY_MONTH = 'anchorSalaryMonth'; //主播收入月榜榜前缀
	const ANCHOR_POPULARITY_MONTH = 'anchorPopularityMonth';//主播人气月榜前缀
	const USER_DEVOTE_MONTH = 'userDevoteMonth';//观众贡献月榜前缀

	const COIN_GIFT_RECORD = 'giftrecordcoin';
	const BEAN_GIFT_RECORD = "giftrecord";
	const REDIS_SIZE = 20;
	const CACHE_DAY_TIME = 600; //日榜缓存时间(单位：秒)
	const CACHE_WEEK_TIME = 36000; //周榜榜缓存时间(单位：秒)
	const CACHE_MONTH_TIME = 86400; //月榜缓存时间(单位：秒)


	public function __construct( $db = null, $redisObj = null )
	{
		if( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new DBHelperi_huanpeng();
		}
		if( $redisObj )
		{
			$this->redis = $redisObj;
		}
		else
		{
			$this->redis = new RedisHelp();
		}

	}

	/**
	 * 获取redis对象
	 *
	 * @return null|RedisHelp
	 */
	public function getRedis()
	{
		return $this->redis;
	}

	private function initRecordTable( $type )
	{
		$gTable = new GiftTable();
		return $gTable->checkTable( $type );//初始化
	}

	/**
	 * 获取本周开始时间到结束时间的日期列表
	 * @return array
	 */
	private function ThisWeekStartEndDateList()
	{
		$week = array();
		$date = date( "Y-m-d" );  //当前日期
		$first = 1; //$first =1 表示每周星期一为开始时间 0表示每周日为开始时间
		$w = date( "w", strtotime( $date ) );  //获取当前周的第几天 周日是 0 周一 到周六是 1 -6
		$d = $w ? $w - $first : 6;  //如果是周日 -6天
		$beginTime = date( "Y-m-d", strtotime( "$date -" . $d . " days" ) ); //本周开始时间
		array_push( $week, $beginTime );
		for ( $i = 1; $i <= 6; $i++ )
		{
			$d = date( "Y-m-d", strtotime( "$beginTime +$i days" ) );  //本周结束时间
			array_push( $week, $d );
		}
		return $week;
	}

	private function ThisMonthStartEndDateList()
	{
		$month = array();
		$d = date( 'd' );
		for ( $i = 1; $i < $d; $i++ )
		{
			$day = date( "Y-m-d", strtotime( "-$i days" ) );  //本月当天之前的日期
			array_push( $month, $day );
		}
		return $month;
	}


	public function setAnchorWeekSalaryToRedis()
	{
		$dateList = self::ThisWeekStartEndDateList();
		if( $dateList )
		{
			$weekKey = self::ANCHOR_SALARY_WEEK;
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{
				if( isset( $dateList[$i] ) )
				{
					$strKey = self::ANCHOR_SALARY . $dateList[$i];
					$everyDay = $this->redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
					foreach ( $everyDay as $k => $v )
					{
						$this->redis->zincrby( $weekKey, $v, $k );
					}
				}
			}
			return $this->redis->zRevRange( $weekKey, 0, $size - 1, true );//TODO加缓存
		}
	}


	/**
	 * 通过redis获取主播人气（昨日日榜）
	 *
	 * @param $size
	 *
	 * @return \type
	 */
	private function _getYesterDayAnchorPopularityToRedis( $size )
	{
		$strKey = self::ANCHOR_POPULARITY . date( "Y-m-d", strtotime( "-1 day" ) );
		return array_keys( $this->redis->zRevRange( $strKey, 0, $size - 1, true ) );
	}




	/**
	 * 通过redis获取主播收益（昨日日榜）
	 *
	 * @param $size  数量
	 *
	 * @return \type
	 */
	private function _getYesterDayAnchorSalaryToRedis( $size )
	{
		$strKey = self::ANCHOR_SALARY . date( "Y-m-d", strtotime( "-1 day" ) );
		return $this->redis->zRevRange( $strKey, 0, $size - 1, true );
	}

	/**
	 *通过redis获取用户贡献（昨日日榜）
	 *
	 * @param $size
	 *
	 * @return \type
	 */
	private function _getYesterDayUserDevoteToRedis( $size )
	{
		$strKey = self::USER_DEVOTE . date( "Y-m-d", strtotime( "-1 day" ) );
		return $this->redis->zRevRange( $strKey, 0, $size - 1, true );
	}

	/**
	 * 获取今天的开始和结束时间
	 * @return array
	 */
	private function _todayTime()
	{
		$stime = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) ) );
		$etime = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, date( 'm' ), date( 'd' ) + 1, date( 'Y' ) ) - 1 );
		return array( 'stime' => $stime, 'etime' => $etime );
	}

	/**
	 * 获取本月的开始和结束时间
	 * @return array
	 */
	private function _monthTime()
	{
		$stime = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, date( 'm' ), 1, date( 'Y' ) ) );
		$etime = date( 'Y-m-d H:i:s', mktime( 23, 59, 59, date( 'm' ), date( 't' ), date( 'Y' ) ) );
		return array( 'stime' => $stime, 'etime' => $etime );
	}


	/**
	 * 收入排行
	 *
	 * @param int    $userType
	 * @param int    $timeType
	 * @param int    $size
	 * @param object $db
	 * @param object $redobj
	 *
	 * @return array
	 */
	public function getSalaryRank( $userType, $timeType, $size )
	{
		if( $userType == self::USER_TYPE_A )//主播收入
		{
			if( $timeType == self::TIME_TYPE_D )
			{//日榜
				$AnchorSalaryDayCache = self::ANCHOR_SALARY . 'DayCache' . date( 'Ymd' );
				$dayCache = $this->redis->get( $AnchorSalaryDayCache );//取缓存
				if( $dayCache )
				{
					$res = json_decode( $dayCache, true );
				}
				else
				{
					$res = $this->_getAnchorSalaryToRedis( $size );
					$this->redis->set( $AnchorSalaryDayCache, json_encode( $res ), self::CACHE_DAY_TIME );
				}
			}
			if( $timeType == self::TIME_TYPE_W )
			{//周榜
				$AnchorSalaryWeekCache = self::ANCHOR_SALARY . 'WeekCache';
				$res = $this->_getAnchorWeekSalaryToRedis( $size );
				if( $res )
				{
					$this->redis->set( $AnchorSalaryWeekCache, json_encode( $res ), self::CACHE_WEEK_TIME );
				}
			}
			if( $timeType == self::TIME_TYPE_M )
			{//月榜
				$AnchorSalaryMonthCache = self::ANCHOR_SALARY . 'MonthCache' . date( 'Ym' );
				$res = $this->_getAnchorMonthSalaryToRedis( $size );
				if( $res )
				{
					$this->redis->set( $AnchorSalaryMonthCache, json_encode( $res ), self::CACHE_MONTH_TIME );
				}
			}
		}
		else
		{//用户贡献
			if( $timeType == self::TIME_TYPE_D )
			{//日榜
				$userDevoteDayCache = self::USER_DEVOTE . 'DayCache' . date( 'Ymd' ) . $size;
				$res = $this->_getUserDevoteToRedis( $size );
				if( $res )
				{
					$this->redis->set( $userDevoteDayCache, json_encode( $res ), self::CACHE_DAY_TIME );
				}
			}
			if( $timeType == self::TIME_TYPE_W )
			{//周榜
				$userDevoteWeekCache = self::USER_DEVOTE . 'WeekCache' . $size;
				$res = $this->_getUserWeekDevoteToRedis( $size );
				if( $res )
				{
					$this->redis->set( $userDevoteWeekCache, json_encode( $res ), self::CACHE_WEEK_TIME );
				}
			}
			if( $timeType == self::TIME_TYPE_M )
			{//月榜
				$userDevoteMonthCache = self::USER_DEVOTE . 'MonthCache' . date( 'Ym' ) . $size;
				$res = $this->_getUserMonthDevoteToRedis( $size );
				if( $res )
				{
					$this->redis->set( $userDevoteMonthCache, json_encode( $res ), self::CACHE_MONTH_TIME );
				}
			}
		}
		return array_keys( $res );
	}

	private function _getGiftRecordCoinByTime( $stime, $etime, $userType )
	{
		if( empty( $stime ) || empty( $etime ) )
		{
			return false;
		}
		if( $userType == self::USER_TYPE_A )
		{//主播收入
			$res = $this->_db->field( 'uid,luid,giftid,giftnum,income,cost' )->where( "ctime >='$stime' and ctime<='$etime'  and otid !=0  and  luid not in (" . WHITE_LIST . ")" )->select( 'giftrecordcoin_' . date( 'Ym' ) );
		}
		if( $userType == self::USER_TYPE_U )
		{//用户贡献

			$res = $this->_db->field( 'uid,luid,giftid,giftnum,income,cost' )->where( "ctime >='$stime' and ctime<='$etime'   and otid !=0 and  uid not in (" . WHITE_LIST . ")" )->select( 'giftrecordcoin_' . date( 'Ym' ) );
		}
		if( $res )
		{
			return $res;
		}
		else
		{
			return array();
		}
	}

	public function _getAnchroProForDbByTime( $stime, $etime )
	{
		if( empty( $stime ) || empty( $etime ) )
		{
			return false;
		}
		$res = $this->_db->field( 'luid,income' )->where( "ctime >='$stime' and otid !=0  and ctime<='$etime '  and luid not in (" . WHITE_LIST . ") " )
			->select( 'giftrecord_' . date( 'Ym' ) );
		if( $res )
		{
			return $res;
		}
		else
		{
			return array();
		}

	}

	/**
	 * 获取昨天的收入排行
	 *
	 * @param int    $userType
	 * @param int    $timeType
	 * @param int    $size
	 * @param object $db
	 * @param object $redobj
	 *
	 * @return array
	 */
	private function getYesterdayRank( $userType, $size, $db )
	{

		$stime = date( 'Y-m-d', strtotime( "-1 day" ) ) . ' 00:00:00';
		$etime = date( 'Y-m-d', strtotime( "-1 day" ) ) . ' 00:00:00';
		if( $userType == self::USER_TYPE_A )
		{
			$yesterdayAnchorSalaryRank = 'yesterdayAnchorSalasyRankCache';
			$yesterdayCache = $this->redis->get( $yesterdayAnchorSalaryRank );//取缓存
			if( $yesterdayCache )
			{
				$res = json_decode( $yesterdayCache, true );
			}
			else
			{
				$res = $this->_getYesterDayAnchorSalaryToRedis( $size );
				if( empty( $res ) )
				{
					$res = self::_getGiftRecordCoinByTime( $stime, $etime, $userType );
				}
				if( $res )
				{
					$this->redis->set( $yesterdayAnchorSalaryRank, json_encode( $res ), 500 );
				}
			}

		}
		else
		{
			$yesterdayUserDevoteRank = 'yesterdayUserDevoteRankCache';
			$yesterdayCache = $this->redis->get( $yesterdayUserDevoteRank );//取缓存
			if( $yesterdayCache )
			{
				$res = json_decode( $yesterdayCache, true );
			}
			else
			{
				$res = $this->_getYesterDayUserDevoteToRedis( $size );
				if( empty( $res ) )
				{
					$res = self::_getGiftRecordCoinByTime( $stime, $etime, $userType );
				}
				if( $res )
				{
					$this->redis->set( $yesterdayUserDevoteRank, json_encode( $res ), 500 );
				}
			}

		}
		return array_keys( $res );
	}

	/**
	 * 获取昨天的人气排行
	 *
	 * @param int    $size
	 * @param object $db
	 * @param object $redobj
	 *
	 * @return array
	 */
	private function getYesterdayPopularityRank( $size, $db )
	{
		$yesterdayPo = array();
		$beginYesterday = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, date( 'm' ), date( 'd' ) - 1, date( 'Y' ) ) );
		$endYesterday = date( 'Y-m-d H:i:s', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) ) - 1 );
		$res = $db->field( 'luid,sum(giftnum) as huancoin' )->where( "ctime >='$beginYesterday'  and ctime<='$endYesterday'  and  otid !=0 and luid not in (" . WHITE_LIST . ")  group by luid" )
			->order( 'huancoin DESC' )->limit( $size )->select( self:: initRecordTable( Gift::SEND_TYPE_BEAN ) );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$Polist[$v['luid']] = $v;
			}
			$yesterdayPo = array_keys( $Polist );
		}
		return $yesterdayPo;
	}


	/**
	 * 获取人气排行榜
	 *
	 * @param int    $userType 用户类型
	 * @param int    $timeType 时间类型
	 * @param int    $size
	 * @param object $db
	 * @param object $redobj
	 *
	 * @return array
	 */
	private function getAuthorPopularityRank( $timeType, $size )
	{
		if( $timeType == self::TIME_TYPE_D )
		{
			$AuthorPopularityDayCache = self::ANCHOR_POPULARITY . 'DayCache' . $size;
			$dayCache = $this->redis->get( $AuthorPopularityDayCache );//取缓存
			if( $dayCache )
			{
				$res = json_decode( $dayCache, true );
			}
			else
			{
				$res = self::_getAnchorPopularityToRedis( $size );
				if( $res )
				{
					$this->redis->set( $AuthorPopularityDayCache, json_encode( $res ), self::CACHE_DAY_TIME );
				}
			}
			if( empty( $res ) )
			{ //redis里面没有则读库 ／／TODO应该先去判断一下redis有没有挂
				$todayTime = $this->_todayTime();
				$res = self::_getAnchroProForDbByTime( $todayTime['stime'], $todayTime['etime'] );
				if( $res )
				{
					$this->redis->set( $AuthorPopularityDayCache, json_encode( $res ), self::CACHE_DAY_TIME );
				}
				else
				{
					$res = array();
				}
			}

		}
		if( $timeType == self::TIME_TYPE_W )
		{
			$AuthorPopularityWeekCache = self::ANCHOR_POPULARITY . 'WeekCache' . $size;
			$weekCache = $this->redis->get( $AuthorPopularityWeekCache );//取缓存
			if( $weekCache )
			{
				$res = json_decode( $weekCache, true );
			}
			else
			{
				$res = self::_getAnchorWeekPopularityToRedis( $size );
				if( $res )
				{
					$this->redis->set( $AuthorPopularityWeekCache, json_encode( $res ), self::CACHE_WEEK_TIME );
				}
			}
			if( empty( $res ) )
			{ //redis里面没有则读库 ／／TODO应该先去判断一下redis有没有挂
				$thisweek = ThisWeekStartEnd();
				$stime = $thisweek['start'] . ' 00:00:00';
				$etime = $thisweek['end'] . ' 23:59:59';
				$res = self::_getAnchroProForDbByTime( $stime, $etime );
				if( $res )
				{
					$this->redis->set( $AuthorPopularityWeekCache, json_encode( $res ), self::CACHE_WEEK_TIME );
				}
				else
				{
					$res = array();
				}
			}
		}
		if( $timeType == self::TIME_TYPE_M )
		{
			$AuthorPopularityMonthCache = self::ANCHOR_POPULARITY . 'MonthCache' . $size;
			$monthCache = $this->redis->get( $AuthorPopularityMonthCache );//取缓存
			if( $monthCache )
			{
				$res = json_decode( $monthCache, true );
			}
			else
			{
				$res = self::_getAnchorMonthPopularityToRedis( $size );
				if( $res )
				{
					$this->redis->set( $AuthorPopularityMonthCache, json_encode( $res ), self::CACHE_MONTH_TIME );
				}
			}
			if( empty( $res ) )
			{ //redis里面没有则读库 ／／TODO应该先去判断一下redis有没有挂
				$monthTime = $this->_monthTime();
				$res = self::_getAnchroProForDbByTime( $monthTime['stime'], $monthTime['etime'] );
				if( $res )
				{

					$this->redis->set( $AuthorPopularityMonthCache, json_encode( $res ), self::CACHE_MONTH_TIME );
				}
				else
				{
					$res = array();
				}
			}
		}
		return array_keys( $res );
	}

	/**
	 * 获取排行
	 *
	 * @param int    $userType 用户类型
	 * @param int    $timeType 时间类型
	 * @param int    $size     请求数量
	 * @param object $db
	 * @param array  $conf
	 *
	 * @return array
	 */
	public function getRanking( $userType, $timeType, $orderType, $size )
	{

		if( $orderType == self::ORDER_TYPE_1 )
		{
			$res = $this->getSalaryRank( $userType, $timeType, $size );
		}
		//人气
		if( $orderType == self::ORDER_TYPE_2 )
		{
			$res = $this->getAuthorPopularityRank( $timeType, $size );

		}
	}

	public function demo()
	{
		$userDevoteDayCache5 = self::ANCHOR_POPULARITY . date( 'Y-m-d' );
		var_dump( $this->redis->get( $userDevoteDayCache5 ) );
		$AuthorPopularityDayCache5 = self::ANCHOR_POPULARITY . 'DayCache' . '5';
		var_dump( $this->redis->get( $AuthorPopularityDayCache5 ) );
		$AuthorPopularityDayCache10 = self::ANCHOR_POPULARITY . 'DayCache' . '10';
		echo $this->redis->del( $AuthorPopularityDayCache10 );
	}


	public function del()
	{
		$AnchorSalaryDayCache = self::ANCHOR_SALARY . 'DayCache' . date( 'Ymd' );
		return $this->redis->del( $AnchorSalaryDayCache );
		$AnchorSalaryWeekCache = self::ANCHOR_SALARY . 'WeekCache';
		echo $this->redis->del( $AnchorSalaryWeekCache );
		$AnchorSalaryMonthCache = self::ANCHOR_SALARY . 'MonthCache' . date( 'Ym' );
		echo $this->redis->del( $AnchorSalaryMonthCache );
		$userDevoteDayCache5 = self::USER_DEVOTE . 'DayCache' . date( 'Ymd' ) . '5';
		echo $this->redis->del( $userDevoteDayCache5 );
		$userDevoteDayCache10 = self::USER_DEVOTE . 'DayCache' . date( 'Ymd' ) . '10';
		echo $this->redis->del( $userDevoteDayCache10 );
		$userDevoteWeekCache5 = self::USER_DEVOTE . 'WeekCache' . '5';
		echo $this->redis->del( $userDevoteWeekCache5 );
		$userDevoteWeekCache10 = self::USER_DEVOTE . 'WeekCache' . '10';
		echo $this->redis->del( $userDevoteWeekCache10 );
		$userDevoteMonthCache5 = self::USER_DEVOTE . 'MonthCache' . date( 'Ym' ) . '5';
		echo $this->redis->del( $userDevoteMonthCache5 );
		$userDevoteMonthCache10 = self::USER_DEVOTE . 'MonthCache' . date( 'Ym' ) . '10';
		echo $this->redis->del( $userDevoteMonthCache10 );
		$yesterdayAnchorSalaryRank = 'yesterdayAnchorSalasyRankCache';
		echo $this->redis->del( $yesterdayAnchorSalaryRank );
		$yesterdayUserDevoteRank = 'yesterdayUserDevoteRankCache';
		echo $this->redis->del( $yesterdayUserDevoteRank );
		$AuthorPopularityDayCache5 = self::ANCHOR_POPULARITY . 'DayCache' . '5';
		echo $this->redis->del( $AuthorPopularityDayCache5 );
		$AuthorPopularityDayCache10 = self::ANCHOR_POPULARITY . 'DayCache' . '10';
		echo $this->redis->del( $AuthorPopularityDayCache10 );
		$AuthorPopularityWeekCache5 = self::ANCHOR_POPULARITY . 'WeekCache' . '5';
		echo $this->redis->del( $AuthorPopularityWeekCache5 );
		$AuthorPopularityWeekCache10 = self::ANCHOR_POPULARITY . 'WeekCache' . '10';
		echo $this->redis->del( $AuthorPopularityWeekCache10 );
		$AuthorPopularityMonthCache5 = self::ANCHOR_POPULARITY . 'MonthCache' . '5';
		echo $this->redis->del( $AuthorPopularityMonthCache5 );
		$AuthorPopularityMonthCache10 = self::ANCHOR_POPULARITY . 'MonthCache' . '10';
		echo $this->redis->del( $AuthorPopularityMonthCache10 );
		$anchorLevelRank = 'anchorLevelRankCache';
		echo $this->redis->del( $anchorLevelRank );
		$strKey = self::ANCHOR_SALARY . date( 'Y-m-d' );
		echo $this->redis->del( $strKey );
		$weekKey = self::ANCHOR_SALARY_WEEK;
		echo $this->redis->del($weekKey);
		$monthKey = self::ANCHOR_SALARY_MONTH;
		echo $this->redis->del($monthKey);
		$strKey = self::USER_DEVOTE . date( 'Y-m-d' );
		echo $this->redis->del( $strKey );
		$weekKey = self::USER_DEVOTE_WEEK;
		echo $this->redis->del($weekKey);
		$monthKey = self::USER_DEVOTE_MONTH;
		echo $this->redis->del($monthKey);
		$strKey = self::ANCHOR_POPULARITY . date( 'Y-m-d' );
		echo $this->redis->del( $strKey );
		$weekKey = self::ANCHOR_POPULARITY_WEEK;
		echo $this->redis->del($weekKey);
		$monthKey = self::ANCHOR_POPULARITY_MONTH;
		echo $this->redis->del($monthKey);
	}

	/**
	 * 主播收益（日榜）
	 */
	private function _anchorSalaryToRedis( $size )
	{
		$strKey = self::ANCHOR_SALARY . date( 'Y-m-d' );
		$todayTime = $this->_todayTime();
		$res = $this->_getGiftRecordCoinByTime( $todayTime['stime'], $todayTime['etime'], self::USER_TYPE_A );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$this->redis->zincrby( $strKey, $v['income'], $v['luid'] );
			}
			return $this->redis->zRevRange( $strKey, 0, $size - 1, true );
		}
	}

	/**主播收益（周榜）*/
	private function _anchorWeekSalaryToRedis( $size )
	{
		$dateList = self::ThisWeekStartEndDateList();
		if( $dateList )
		{
			$weekKey = self::ANCHOR_SALARY_WEEK;
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{

				$strKey = self::ANCHOR_SALARY . $dateList[$i];
				$stime = $dateList[$i] . ' 00:00:00';
				$etime = $dateList[$i] . ' 23:59:59';
				$res = $this->_getGiftRecordCoinByTime( $stime, $etime, self::USER_TYPE_A );
				foreach ( $res as $k => $v )
				{
					$this->redis->zincrby( $strKey, $v['income'], $v['luid'] );
				}
				$everyDay = $this->redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
				foreach ( $everyDay as $k1 => $v1 )
				{
					$this->redis->zincrby( $weekKey, $v1, $k1 );
				}
			}
			return $this->redis->zRevRange( $weekKey, 0, $size - 1, true );//TODO加缓存
		}
		else
		{
			return array();
		}

	}

	/**
	 * 主播收益（月榜）
	 */
	private function _anchorMonthSalaryToRedis( $size )
	{
		$dateList = self::ThisMonthStartEndDateList();
		if( $dateList )
		{
			$monthKey = self::ANCHOR_SALARY_MONTH;
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{

				$strKey = self::ANCHOR_SALARY . $dateList[$i];
				$stime = $dateList[$i] . ' 00:00:00';
				$etime = $dateList[$i] . ' 23:59:59';
				$res = $this->_getGiftRecordCoinByTime( $stime, $etime, self::USER_TYPE_A );
				foreach ( $res as $k => $v )
				{
					$this->redis->zincrby( $strKey, $v['income'], $v['luid'] );
				}
				$everyDay = $this->redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
				foreach ( $everyDay as $k1 => $v1 )
				{
					$this->redis->zincrby( $monthKey, $v1, $k1 );
				}
			}
			return $this->redis->zRevRange( $monthKey, 0, $size - 1, true );//TODO加缓存
		}
		else
		{
			return array();
		}

	}

	/**
	 *用户贡献（日榜）
	 */
	private function _getUserDevoteToRedis( $size )
	{
		$strKey = self::USER_DEVOTE . date( 'Y-m-d' );
		$todayTime = $this->_todayTime();
		$res = $this->_getGiftRecordCoinByTime( $todayTime['stime'], $todayTime['etime'], self::USER_TYPE_U );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$this->redis->zincrby( $strKey, $v['cost'], $v['uid'] );
			}
			return $this->redis->zRevRange( $strKey, 0, $size - 1, true );
		}

	}

	/**用户贡献（周榜）*/
	private function _getUserWeekDevoteToRedis( $size )
	{
		$dateList = self::ThisWeekStartEndDateList();
		if( $dateList )
		{
			$weekKey = self::USER_DEVOTE_WEEK;
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{

				$strKey = self::USER_DEVOTE . $dateList[$i];
				$stime = $dateList[$i] . ' 00:00:00';
				$etime = $dateList[$i] . ' 23:59:59';
				$res = $this->_getGiftRecordCoinByTime( $stime, $etime, self::USER_TYPE_A );
				foreach ( $res as $k => $v )
				{
					$this->redis->zincrby( $strKey, $v['income'], $v['luid'] );
				}
				$everyDay = $this->redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
				foreach ( $everyDay as $k1 => $v1 )
				{
					$this->redis->zincrby( $weekKey, $v1, $k1 );
				}
			}
			return $this->redis->zRevRange( $weekKey, 0, $size - 1, true );//TODO加缓存
		}
		else
		{
			return array();
		}

	}

	/**
	 * 主播收益（月榜）
	 */
	private function _getUserMonthDevoteToRedis( $size )
	{
		$dateList = self::ThisMonthStartEndDateList();
		if( $dateList )
		{
			$monthKey = self::USER_DEVOTE_MONTH;
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{

				$strKey = self::USER_DEVOTE . $dateList[$i];
				$stime = $dateList[$i] . ' 00:00:00';
				$etime = $dateList[$i] . ' 23:59:59';
				$res = $this->_getGiftRecordCoinByTime( $stime, $etime, self::USER_TYPE_A );
				foreach ( $res as $k => $v )
				{
					$this->redis->zincrby( $strKey, $v['income'], $v['luid'] );
				}
				$everyDay = $this->redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
				foreach ( $everyDay as $k1 => $v1 )
				{
					$this->redis->zincrby( $monthKey, $v1, $k1 );
				}
			}
			return $this->redis->zRevRange( $monthKey, 0, $size - 1, true );//TODO加缓存
		}
		else
		{
			return array();
		}

	}


	/**
	 * 通过redis获取主播人气（日榜）
	 *
	 * @param $size
	 *
	 * @return \type
	 */
	public function _getAnchorPopularityToRedis( $size )
	{
		$strKey = self::ANCHOR_POPULARITY . date( 'Y-m-d' );
		$todayTime = $this->_todayTime();
		$res = self::_getAnchroProForDbByTime( $todayTime['stime'], $todayTime['etime'] );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$this->redis->zincrby( $strKey, $v['income'], $v['luid'] );
			}
			return $this->redis->zRevRange( $strKey, 0, $size - 1, true );
		}

	}

	/**通过redis获取主播人气（周榜）
	 *
	 * @param int $size 数量
	 *
	 * @return array|\type
	 */
	public function _getAnchorWeekPopularityToRedis( $size )
	{
		$dateList = self::ThisWeekStartEndDateList();
		if( $dateList )
		{
			$weekKey = self::ANCHOR_POPULARITY_WEEK;
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{
				if( isset( $dateList[$i] ) )
				{
					$strKey = self::ANCHOR_POPULARITY . $dateList[$i];
					$stime = $dateList[$i] . ' 00:00:00';
					$etime = $dateList[$i] . ' 23:59:59';
					$res = self::_getAnchroProForDbByTime( $stime, $etime );
					foreach ( $res as $k => $v )
					{
						$this->redis->zincrby( $strKey, $v['income'], $v['luid'] );
					}
					$everyDay = $this->redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
					foreach ( $everyDay as $k1 => $v1 )
					{
						$this->redis->zincrby( $weekKey, $v1, $k1 );
					}
				}
			}
			return $this->redis->zRevRange( $weekKey, 0, $size - 1, true );//TODO加缓存
		}
		else
		{
			return array();
		}

	}

	/**通过redis获取主播人气（月榜）
	 *
	 * @param int $size 数量
	 *
	 * @return array|\type
	 */
	public function _getAnchorMonthPopularityToRedis( $size )
	{
		$dateList = self::ThisMonthStartEndDateList();
		if( $dateList )
		{
			$monthKey = self::ANCHOR_POPULARITY_MONTH;
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{
				if( isset( $dateList[$i] ) )
				{
					$strKey = self::ANCHOR_POPULARITY . $dateList[$i];
					$stime = $dateList[$i] . ' 00:00:00';
					$etime = $dateList[$i] . ' 23:59:59';
					$res = self::_getAnchroProForDbByTime( $stime, $etime );
					foreach ( $res as $k => $v )
					{
						$this->redis->zincrby( $strKey, $v['income'], $v['luid'] );
					}
					$everyDay = $this->redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
					foreach ( $everyDay as $k1 => $v1 )
					{
						$this->redis->zincrby( $monthKey, $v1, $k1 );
					}
				}
			}
			return $this->redis->zRevRange( $monthKey, 0, $size - 1, true );//TODO加缓存
		}
		else
		{
			return array();
		}

	}

	private function salary( $size )
	{
		self::_anchorSalaryToRedis( $size );
		self::_anchorWeekSalaryToRedis( $size );
		self::_anchorMonthSalaryToRedis( $size );
	}

	public function devote( $size )
	{
		self::_getUserDevoteToRedis( $size );
		self::_getUserWeekDevoteToRedis( $size );
		self::_getUserMonthDevoteToRedis( $size );
	}

	public function popularity( $size )
	{
		var_dump(self::_getAnchorPopularityToRedis( $size ));
		var_dump(self::_getAnchorWeekPopularityToRedis( $size ));
		var_dump(self::_getAnchorMonthPopularityToRedis( $size ));
	}

//  public  function  run ($size){
//	  self::salary( $size );
//	  self::devote( $size );
//	  self::popularity( $size );
//  }
public  function  de(){
	$strKey = self::ANCHOR_POPULARITY . date( 'Y-m-d' );
	return $this->redis->zRevRange( $strKey, 0, 9, true );
}


}


//$strKey = self::ANCHOR_POPULARITY . date( 'Y-m-d' );
$obj = new Rank();

//$res = $obj->demo();
//var_dump( $res );
//$res=$obj->del();
//var_dump( $res );
//$obj->devote(10);

$r=$obj->popularity(10);
var_dump($r);
