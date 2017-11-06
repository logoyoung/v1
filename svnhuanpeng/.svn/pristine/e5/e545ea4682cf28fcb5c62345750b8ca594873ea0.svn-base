<?php
/**
 * Created by PhpStorm.
 * User: Dylan
 * Date: 17/7/31 下午4:11
 * CopyRight: huanpeng.com
 */

namespace Cli\Controller;


class RankController extends \Think\Controller
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
	const CACHE_MONTH_TIME = 36000; //月榜缓存时间(单位：秒)



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
		$mStart=date('Y-m-01', strtotime(date("Y-m-d")));
		for ( $i = 0; $i < $d; $i++ )
		{
			$day = date( "Y-m-d", strtotime( "-$i days" ) );  //本月当天之前的日期
			if(strtotime($day)>=strtotime($mStart)){
				array_push( $month, $day );
			}
		}
		return $month;
	}

	/**
	 * 通过redis获取主播收益（日榜）
	 *
	 * @param $size  数量
	 *
	 * @return \type
	 */
	private function _getAnchorSalaryToRedis( $size )
	{
		$strKey = self::ANCHOR_SALARY . date( 'Y-m-d' );
		return $this->redis->zRevRange( $strKey, 0, $size - 1, true );
	}

	/**通过redis获取主播收益（周榜）
	 *
	 * @param int $size 数量
	 *
	 * @return array|\type
	 */
	public function _getAnchorWeekSalaryToRedis( $size )
	{
		$dateList = self::ThisWeekStartEndDateList();
		if( $dateList )
		{
			$weekKey = self::ANCHOR_SALARY_WEEK.'_' . $this->getYearWeek();
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
		else
		{
			return array();
		}

	}

	/**通过redis获取主播收益（月榜）
	 *
	 * @param int $size 数量
	 *
	 * @return array|\type
	 */
	public function _getAnchorMonthSalaryToRedis( $size )
	{
		$dateList = self::ThisMonthStartEndDateList();
		if( $dateList )
		{
			$monthKey = self::ANCHOR_SALARY_MONTH. '_' . $this->getYearMonth();
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{
				if( isset( $dateList[$i] ) )
				{
					$strKey = self::ANCHOR_SALARY . $dateList[$i];
					$everyDay = $this->redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
					foreach ( $everyDay as $k => $v )
					{
						$this->redis->zincrby( $monthKey, $v, $k );
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


//同步收入月榜
	public function smonth( $size )
	{
		$AnchorSalaryMonthCache = self::ANCHOR_SALARY . 'MonthCache' . date( 'Ym' );
		$redis = new \Think\Cache\Driver\Redis();
		$redis->delete($AnchorSalaryMonthCache);
		$dateList = self::ThisMonthStartEndDateList();
		if( $dateList )
		{
			$monthKey = self::ANCHOR_SALARY_MONTH. '_' . $this->getYearMonth();
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{
				if( isset( $dateList[$i] ) )
				{
					$strKey = self::ANCHOR_SALARY . $dateList[$i];
					$everyDay = $redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
					foreach ( $everyDay as $k => $v )
					{
						$redis->zincrby( $monthKey, $v, $k );
					}
				}
			}
			$res= $redis->zRevRange( $monthKey, 0, $size - 1, true );//TODO加缓存
		}
		else
		{
			$res=array();
		}
		$redis->set( $AnchorSalaryMonthCache, json_encode( $res ), self::CACHE_MONTH_TIME );
        dump($res);
	}




//同步贡献月榜
	public function devote( $size )
	{
		$dateList = self::ThisMonthStartEndDateList();
		$userDevoteMonthCache = self::USER_DEVOTE . 'MonthCache' . date( 'Ym' ) . $size;
		$redis = new \Think\Cache\Driver\Redis();
		if( $dateList )
		{
			$monthKey = self::USER_DEVOTE_MONTH. '_' . $this->getYearMonth();
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{
				if( isset( $dateList[$i] ) )
				{
					$strKey = self::USER_DEVOTE . $dateList[$i];
					$everyDay = $redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
					foreach ( $everyDay as $k => $v )
					{
						$redis->zincrby( $monthKey, $v, $k );
					}
				}
			}
			$res= $redis->zRevRange( $monthKey, 0, $size - 1, true );//TODO加缓存
		}
		else
		{
			$res= array();
		}
		$redis->set( $userDevoteMonthCache, json_encode( $res ), self::CACHE_MONTH_TIME );
		dump($res);
	}
//同步人气月榜
	public function populary( $size )
	{
		$AuthorPopularityMonthCache = self::ANCHOR_POPULARITY . 'MonthCache' . $size;
		$redis = new \Think\Cache\Driver\Redis();
		$dateList = self::ThisMonthStartEndDateList();
		if( $dateList )
		{
			$monthKey = self::ANCHOR_POPULARITY_MONTH. '_' . $this->getYearMonth();
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{
				if( isset( $dateList[$i] ) )
				{
					$strKey = self::ANCHOR_POPULARITY . $dateList[$i];
					$everyDay = $redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
					foreach ( $everyDay as $k => $v )
					{
						$this->redis->zincrby( $monthKey, $v, $k );
					}
				}
			}
			$res= $redis->zRevRange( $monthKey, 0, $size - 1, true );//TODO加缓存
		}
		else
		{
			$res= array();
		}
		$redis->set($AuthorPopularityMonthCache, json_encode( $res ), self::CACHE_MONTH_TIME );
		dump($res);

	}

	/**
	 *通过redis获取用户贡献（日榜）
	 *
	 * @param $size
	 *
	 * @return \type
	 */
	private function _getUserDevoteToRedis( $size )
	{
		$strKey = self::USER_DEVOTE . date( 'Y-m-d' );
		return $this->redis->zRevRange( $strKey, 0, $size - 1, true );
	}

	/**通过redis获取用户贡献（周榜）
	 *
	 * @param int $size 数量
	 *
	 * @return array|\type
	 */
	public function _getUserWeekDevoteToRedis( $size )
	{
		$dateList = self::ThisWeekStartEndDateList();
		if( $dateList )
		{
			$weekKey = self::USER_DEVOTE_WEEK . '_' . $this->getYearWeek();
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{
				if( isset( $dateList[$i] ) )
				{
					$strKey = self::USER_DEVOTE . $dateList[$i];
					$everyDay = $this->redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
					foreach ( $everyDay as $k => $v )
					{
						$this->redis->zincrby( $weekKey, $v, $k );
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

	public function getYearWeek()
	{
		return date( 'YW' );
	}

	public function  getYearMonth(){
		return date( 'Ym' );
	}
	/**通过redis获取用户贡献（月榜）
	 *
	 * @param int $size 数量
	 *
	 * @return array|\type
	 */
	public function _getUserMonthDevoteToRedis( $size )
	{
		$dateList = self::ThisMonthStartEndDateList();
		if( $dateList )
		{
			$monthKey = self::USER_DEVOTE_MONTH. '_' . $this->getYearMonth();
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{
				if( isset( $dateList[$i] ) )
				{
					$strKey = self::USER_DEVOTE . $dateList[$i];
					$everyDay = $this->redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
					foreach ( $everyDay as $k => $v )
					{
						$this->redis->zincrby( $monthKey, $v, $k );
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

	/**
	 * 通过redis获取主播人气（日榜）
	 *
	 * @param $size
	 *
	 * @return \type
	 */
	private function _getAnchorPopularityToRedis( $size )
	{
		$strKey = self::ANCHOR_POPULARITY . date( 'Y-m-d' );
		return $this->redis->zRevRange( $strKey, 0, $size - 1, true );
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
			$weekKey = self::ANCHOR_POPULARITY_WEEK. '_' . $this->getYearWeek();
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{
				if( isset( $dateList[$i] ) )
				{
					$strKey = self::ANCHOR_POPULARITY . $dateList[$i];
					$everyDay = $this->redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
					foreach ( $everyDay as $k => $v )
					{
						$this->redis->zincrby( $weekKey, $v, $k );
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
			$monthKey = self::ANCHOR_POPULARITY_MONTH. '_' . $this->getYearMonth();
			for ( $i = 0, $k = count( $dateList ); $i < $k; $i++ )
			{
				if( isset( $dateList[$i] ) )
				{
					$strKey = self::ANCHOR_POPULARITY . $dateList[$i];
					$everyDay = $this->redis->zRevRange( $strKey, 0, self::REDIS_SIZE, true );
					foreach ( $everyDay as $k => $v )
					{
						$this->redis->zincrby( $monthKey, $v, $k );
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
	private function getSalaryRank( $userType, $timeType, $size )
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
					if( $res )
					{
						$this->redis->set( $AnchorSalaryDayCache, json_encode( $res ), self::CACHE_DAY_TIME );
					}
				}
				if( empty( $res ) )
				{ //redis里面没有则读库 ／／TODO应该先去判断一下redis有没有挂
					$todayTime = $this->_todayTime();
					$res = $this->_getGiftRecordCoinByTime( $todayTime['stime'], $todayTime['etime'], $userType, $size );
					if( $res )
					{
						$this->redis->set( $AnchorSalaryDayCache, json_encode( $res ), self::CACHE_DAY_TIME );
					}
					else
					{
						$res = array();
					}
				}
			}
			if( $timeType == self::TIME_TYPE_W )
			{//周榜
				$AnchorSalaryWeekCache = self::ANCHOR_SALARY . 'WeekCache';
				$WeekCache = $this->redis->get( $AnchorSalaryWeekCache );//取缓存
				if( $WeekCache )
				{
					$res = json_decode( $WeekCache, true );
				}
				else
				{
					$res = $this->_getAnchorWeekSalaryToRedis( $size );
					if( $res )
					{
						$this->redis->set( $AnchorSalaryWeekCache, json_encode( $res ), self::CACHE_WEEK_TIME );
					}
				}
				if( empty( $res ) )
				{ //redis里面没有则读库 ／／TODO应该先去判断一下redis有没有挂
					$thisweek = ThisWeekStartEnd();
					$stime = $thisweek['start'] . ' 00:00:00';
					$etime = $thisweek['end'] . ' 23:59:59';
					$res = $this->_getGiftRecordCoinByTime( $stime, $etime, $userType, $size );
					if( $res )
					{
						$this->redis->set( $AnchorSalaryWeekCache, json_encode( $res ), self::CACHE_WEEK_TIME );
					}
					else
					{
						$res = array();
					}
				}

			}
			if( $timeType == self::TIME_TYPE_M )
			{//月榜
				$AnchorSalaryMonthCache = self::ANCHOR_SALARY . 'MonthCache' . date( 'Ym' );
				$MonthCache = $this->redis->get( $AnchorSalaryMonthCache );//取缓存
				if( $MonthCache )
				{
					$res = json_decode( $MonthCache, true );
				}
				else
				{
					$res = $this->_getAnchorMonthSalaryToRedis( $size );
					if( $res )
					{
						$this->redis->set( $AnchorSalaryMonthCache, json_encode( $res ), self::CACHE_MONTH_TIME );
					}
				}
				if( empty( $res ) )
				{ //redis里面没有则读库 ／／TODO应该先去判断一下redis有没有挂
					$monthTime = $this->_monthTime();
					$res = $this->_getGiftRecordCoinByTime( $monthTime['stime'], $monthTime['etime'], $userType, $size );
					if( $res )
					{
						$this->redis->set( $AnchorSalaryMonthCache, json_encode( $res ), self::CACHE_MONTH_TIME );
					}
					else
					{
						$res = array();
					}
				}
			}
		}
		else
		{//用户贡献
			if( $timeType == self::TIME_TYPE_D )
			{//日榜
				$userDevoteDayCache = self::USER_DEVOTE . 'DayCache' . date( 'Ymd' ) . $size;
				$dayCache = $this->redis->get( $userDevoteDayCache );//取缓存
				if( $dayCache )
				{
					$res = json_decode( $dayCache, true );
				}
				else
				{
					$res = $this->_getUserDevoteToRedis( $size );
					if( $res )
					{
						$this->redis->set( $userDevoteDayCache, json_encode( $res ), self::CACHE_DAY_TIME );
					}
				}
				if( empty( $res ) )
				{ //redis里面没有则读库 ／／TODO应该先去判断一下redis有没有挂
					$todayTime = $this->_todayTime();
					$res = $this->_getGiftRecordCoinByTime( $todayTime['stime'], $todayTime['etime'], $userType, $size );
					if( $res )
					{
						$this->redis->set( $userDevoteDayCache, json_encode( $res ), self::CACHE_DAY_TIME );
					}
					else
					{
						$res = array();
					}
				}
			}
			if( $timeType == self::TIME_TYPE_W )
			{//周榜
				$userDevoteWeekCache = self::USER_DEVOTE . 'WeekCache' . $size;
				$WeekCache = $this->redis->get( $userDevoteWeekCache );//取缓存
				if( $WeekCache )
				{
					$res = json_decode( $WeekCache, true );
				}
				else
				{
					$res = $this->_getUserWeekDevoteToRedis( $size );

					if( $res )
					{
						$this->redis->set( $userDevoteWeekCache, json_encode( $res ), self::CACHE_WEEK_TIME );
					}
				}
				if( empty( $res ) )
				{ //redis里面没有则读库 ／／TODO应该先去判断一下redis有没有挂
					$thisweek = ThisWeekStartEnd();
					$stime = $thisweek['start'] . ' 00:00:00';
					$etime = $thisweek['end'] . ' 23:59:59';
					$res = $this->_getGiftRecordCoinByTime( $stime, $etime, $userType, $size );
					if( $res )
					{
						$this->redis->set( $userDevoteWeekCache, json_encode( $res ), self::CACHE_WEEK_TIME );
					}
					else
					{
						$res = array();
					}
				}

			}
			if( $timeType == self::TIME_TYPE_M )
			{//月榜
				$userDevoteMonthCache = self::USER_DEVOTE . 'MonthCache' . date( 'Ym' ) . $size;
				$MonthCache = $this->redis->get( $userDevoteMonthCache );//取缓存
				if( $MonthCache )
				{
					$res = json_decode( $MonthCache, true );
				}
				else
				{
					$res = $this->_getUserMonthDevoteToRedis( $size );
					if( $res )
					{
						$this->redis->set( $userDevoteMonthCache, json_encode( $res ), self::CACHE_MONTH_TIME );
					}
				}
				if( empty( $res ) )
				{ //redis里面没有则读库 ／／TODO应该先去判断一下redis有没有挂
					$monthTime = $this->_monthTime();
					$res = $this->_getGiftRecordCoinByTime( $monthTime['stime'], $monthTime['etime'], $userType, $size );
					if( $res )
					{
						$this->redis->set( $userDevoteMonthCache, json_encode( $res ), self::CACHE_MONTH_TIME );
					}
					else
					{
						$res = array();
					}
				}
			}
		}
		return array_keys( $res );
	}

	private function _getGiftRecordCoinByTime( $stime, $etime, $userType, $size )
	{
		if( empty( $stime ) || empty( $etime ) )
		{
			return false;
		}
		if( $userType == self::USER_TYPE_A )
		{//主播收入
			$order = 'luid';
			$type = Gift::SEND_TYPE_COIN;
			$res = $this->_db->field( 'uid,luid,giftid,giftnum' )->where( "ctime >='$stime' and ctime<='$etime'  and otid !=0  and  luid not in (" . WHITE_LIST . ")" )->select( self:: initRecordTable( $type ) );
		}
		if( $userType == self::USER_TYPE_U )
		{//用户贡献
			$order = 'uid';
			$type = Gift::SEND_TYPE_BEAN;
			$res = $this->_db->field( 'uid,luid,giftid,giftnum' )->where( "ctime >='$stime' and ctime<='$etime'   and otid !=0 and  uid not in (" . WHITE_LIST . ")" )->select( self:: initRecordTable( $type ) );
		}
		if( $res )
		{
			$temp = array();
			foreach ( $res as $v )
			{
				$giftSalary = Gift::_getGiftTotal( $v['giftid'], $v['giftnum'], $type );
				if( array_key_exists( $v[$order], $temp ) )
				{

					$temp[$v[$order]] = $giftSalary + $temp[$v[$order]];
				}
				else
				{
					$temp[$v[$order]] = $giftSalary;
				}
			}
			arsort( $temp );
			$temp = array_slice( $temp, 0, $size, true );
			return $temp;
		}
		else
		{
			return array();
		}
	}

	private function _getAnchroProForDbByTime( $stime, $etime, $size )
	{
		$list = array();
		if( empty( $stime ) || empty( $etime ) )
		{
			return false;
		}
		$res = $this->_db->field( 'luid,sum(giftnum) as coin' )->where( "ctime >='$stime' and otid !=0  and ctime<='$etime '  and luid not in (" . WHITE_LIST . ")  group by luid" )
			->order( 'coin DESC' )->limit( $size )->select( self:: initRecordTable( Gift::SEND_TYPE_BEAN ) );
		if( $res )
		{
			foreach ( $res as $v )
			{
				$list[$v['luid']] = $v['coin'];
			}
		}
		return $list;
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
					$res = self::_getGiftRecordCoinByTime( $stime, $etime, $userType, $size );
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
					$res = self::_getGiftRecordCoinByTime( $stime, $etime, $userType, $size );
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
				$res = self::_getAnchroProForDbByTime( $todayTime['stime'], $todayTime['etime'], $size );
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
				$res = self::_getAnchroProForDbByTime( $stime, $etime, $size );
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
				$res = self::_getAnchroProForDbByTime( $monthTime['stime'], $monthTime['etime'], $size );
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
	public function getRanking( $userType, $timeType, $orderType, $size, $db = null, $conf = null )
	{
		$listIds = $newRes = $rankList = $rankListes = $todayUids = $yesterdayUids = array();
		$userService = new UserDataService();
		if( !$db )
		{
			$db = $this->_db;
		}

		if( !$conf )
		{
			$conf = $GLOBALS['env-def'][$GLOBALS['env']];
		}

		if( $orderType == self::ORDER_TYPE_1 )
		{
			$res = $this->getSalaryRank( $userType, $timeType, $size );
			if( $res )
			{
//				$list = User::getUsersInfoByUids( $res, $db, $base = User::USER_INFO_BASE );
				$userService->setUid( $res );
				$userData = $userService->getUserInfo();
				$authorStatus = array_unique( Anchor::checkAuthoerIsLive( $res, $db ) );
				if( $userType == self::USER_TYPE_A )
				{
					$roomids = Anchor::getRoomIDs( $res, $db );
					$userLevel = Anchor::getAnchorsLevelByUids( $res, $db );
				}
				else
				{
					$roomids = array();
					$userLevel = User::getUserLevelByUids( $res, $db );
				}
				for ( $i = 0, $k = count( $res ); $i < $k; $i++ )
				{
					if( isset( $userData[$res[$i]] ) )
					{
						$rankList['uid'] = $res[$i];
						$rankList['head'] = $userData[$res[$i]]['pic'] ? $userData[$res[$i]]['pic'] : DEFAULT_PIC;
						$rankList['nick'] = $userData[$res[$i]]['nick'];
						$rankList['money'] = 0;
						$rankList['roomID'] = array_key_exists( $res[$i], $roomids ) ? $roomids[$res[$i]] : 0;
						$rankList['level'] = array_key_exists( $res[$i], $userLevel ) ? $userLevel[$res[$i]]['level'] : 1;
						if( in_array( $res[$i], $authorStatus ) )
						{
							$rankList['isliving'] = 1;
						}
						else
						{
							$rankList['isliving'] = 0;
						}
						array_push( $rankListes, $rankList );
						array_push( $todayUids, $res[$i] );
					}
				}
				if( ( $userType == self::USER_TYPE_A && $timeType == self::TIME_TYPE_D ) || ( $userType == self::USER_TYPE_U && $timeType == self::TIME_TYPE_D ) )
				{
					for ( $i = 0, $j = count( $rankListes ); $i < $j; $i++ )
					{
						$rankListes[$i]['status'] = '1';
					}
					$yesterdayUids = $this->getYesterdayRank( $userType, $size, $db );
					$existCommont = array_intersect( $todayUids, $yesterdayUids );
					$diffkey = array_keys( $existCommont );
					if( $existCommont )
					{
						$yesterdayUids = array_flip( $yesterdayUids );
						for ( $m = 0, $n = count( $diffkey ); $m < $n; $m++ )
						{
							$number = $existCommont[$diffkey[$m]];
							$yesterdays = $yesterdayUids[$number];
							if( $diffkey[$m] > $yesterdays )
							{
								$rankListes[$diffkey[$m]]['status'] = '-1';
							}
							if( $diffkey[$m] == $yesterdays )
							{
								$rankListes[$diffkey[$m]]['status'] = '0';
							}
						}
					}
				}
			}
			else
			{
				$rankListes = array();
			}
			return $rankListes;
		}
		//根据等级排行
		if( $orderType == self::ORDER_TYPE_3 )
		{
			$anchorLevelRank = 'anchorLevelRankCache';
			$dayCache = $this->redis->get( $anchorLevelRank );//取缓存
			if( $dayCache )
			{
				$res = json_decode( $dayCache, true );
			}
			else
			{
				$res = Anchor::getAnchorlevelRank( $size, $db );
				if( $res )
				{
					$this->redis->set( $anchorLevelRank, json_encode( $res ), self::CACHE_DAY_TIME );
				}
			}
			if( $res )
			{
				$uids = array_keys( $res );
				$rankListes = $levelList = array();
				$userService->setUid( $uids );
				$userData = $userService->getUserInfo();
//				$userInfo = User::getUsersInfoByUids( $uids, $db, $base = User::USER_INFO_BASE );
				$authorStatus = array_unique( Anchor::checkAuthoerIsLive( $uids, $db ) );
				$roomids = Anchor::getRoomIDs( $uids, $db );
				foreach ( $res as $k => $v )
				{
					$levelList['uid'] = $v['uid'];
					$levelList['head'] = $userData[$v['uid']]['pic'] ? $userData[$v['uid']]['pic'] : '';
					$levelList['nick'] = $userData[$v['uid']]['nick'];
					$levelList['level'] = $v['level'];
					$levelList['roomID'] = array_key_exists( $v['uid'], $roomids ) ? $roomids[$v['uid']] : 0;
					$levelList['money'] = $v['level']; //这里是为了前端便于显示所以这样组织的
					$levelList['integral'] = $v['integral'];
					if( in_array( $v['uid'], $authorStatus ) )
					{
						$levelList['isliving'] = 1;
					}
					else
					{
						$levelList['isliving'] = 0;
					}
					array_push( $rankListes, $levelList );
				}
				//如果等级相同,贡献度大的排在前面
				foreach ( $rankListes as $key => $value )
				{
					$money[$key] = $value['money'];
					$integral[$key] = $value['integral'];
				}
				array_multisort( $money, SORT_NUMERIC, SORT_DESC, $integral, SORT_STRING, SORT_DESC, $rankListes );
			}
			return $rankListes;
		}
		//人气
		if( $orderType == self::ORDER_TYPE_2 )
		{
			$res = $this->getAuthorPopularityRank( $timeType, $size );
			if( $res )
			{
				$rankListes = $levelList = array();
				$userInfo = User::getUsersInfoByUids( $res, $db, $base = User::USER_INFO_BASE );
				$userService->setUid( $res );
				$userData = $userService->getUserInfo();
				$authorLevel = Anchor::getAnchorsLevelByUids( $res, $db );
				$roomids = Anchor::getRoomIDs( $res, $db );
				$authorStatus = array_unique( Anchor::checkAuthoerIsLive( $res, $db ) );
				for ( $i = 0, $k = count( $res ); $i < $k; $i++ )
				{
					if( isset( $userInfo[$res[$i]] ) )
					{
						$levelList['uid'] = $res[$i];
						$levelList['head'] = $userData[$res[$i]]['pic'] ? $userData[$res[$i]]['pic'] : '';
						$levelList['nick'] = $userData[$res[$i]]['nick'];
						$levelList['money'] = 0;
						$levelList['roomID'] = array_key_exists( $res[$i], $roomids ) ? $roomids[$res[$i]] : 0;
						$levelList['level'] = array_key_exists( $res[$i], $authorLevel ) ? $authorLevel[$res[$i]]['level'] : 1;
						if( in_array( $res[$i], $authorStatus ) )
						{
							$levelList['isliving'] = 1;
						}
						else
						{
							$levelList['isliving'] = 0;
						}
						array_push( $rankListes, $levelList );
					}
				}
				if( ( $userType == self::USER_TYPE_A && $timeType == self::TIME_TYPE_D ) )
				{
					for ( $i = 0, $j = count( $rankListes ); $i < $j; $i++ )
					{
						$rankListes[$i]['status'] = '1';
					}
					$yesterdayrank = self::_getYesterDayAnchorPopularityToRedis( $size );
					if( empty( $yesterdayrank ) )
					{
						$yesterdayrank = self::getYesterdayPopularityRank( $size, $db );
					}
					$existCommont = array_intersect( $res, $yesterdayrank );
					$diffkey = array_keys( $existCommont );
					if( $existCommont )
					{
						$yesterdayrank = array_flip( $yesterdayrank );
						for ( $m = 0, $n = count( $diffkey ); $m < $n; $m++ )
						{
							$number = $existCommont[$diffkey[$m]];
							$yesterdays = $yesterdayrank[$number];
							if( $diffkey[$m] > $yesterdays )
							{
								$rankListes[$diffkey[$m]]['status'] = '-1';
							}
							if( $diffkey[$m] == $yesterdays )
							{
								$rankListes[$diffkey[$m]]['status'] = '0';
							}
						}
					}
				}
			}
			else
			{
				$rankListes = array();
			}
			return $rankListes;
		}
	}

}