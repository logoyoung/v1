<?php

class RankUpdate
{
	public  $luid;
	public  $rankDay;
	public  $rankWeek;
	public  $rankAll;
	public  $timestamp;
	public  $pre = "room:rank:";
	private $db;
	private $redis;
	private $lroom;

	function __construct( $luid, $redis = null, $db = null, $lroom = null )
	{
		$this->luid = (int)$luid;
		if ( !$this->luid )
		{
			return false;
		}
		$this->pre = $this->pre . $this->luid;
		if ( $db )
		{
			$this->db = $db;
		}
		else
		{
			$this->db = new DBHelperi_huanpeng();
		}
		if ( $redis )
		{
			$this->redis = $redis;
		}
		else
		{
			$this->redis = new RedisHelp();
		}
		if ( $lroom )
		{
			$this->lroom = $lroom;
		}
		else
		{
			$this->lroom = new LiveRoom( $luid, $this->db );
		}
		if ( !$this->getDRankUTimestamp() )
		{
			$this->setDRankUTimestamp();
		}
		else
		{
			if ( date( 'd' ) != date( 'd', $this->getDRankUTimestamp() ) && time() > $this->getDRankUTimestamp() )
			{
				$this->delRankDayList();
				$this->setDRankUTimestamp();
			}
		}
		if ( !$this->getWRankUTimestamp() )
		{
			$this->setWRankUTimestamp();
		}
		else
		{
			if ( time() - $this->getWRankUTimestamp() > 604800 )
			{
				$this->delRankWeekList();
				$this->setWRankUTimestamp();
			}
		}
		$this->dayTimestamp  = $this->getDRankUTimestamp();
		$this->weekTimestamp = $this->getWRankUTimestamp();
		$this->rankDay       = $this->pre . ":day";
		$this->rankWeek      = $this->pre . ":week";
		$this->rankAll       = $this->pre;

		return true;
	}

	function setDRankUTimestamp()
	{
		$time = time();
		$this->redis->set( 'roomrankDayUtimestamp', "$time" );
	}

	function getDRankUTimestamp()
	{
		return (int)$this->redis->get( 'roomrankDayUtimestamp' );
	}

	function setWRankUTimestamp()
	{
		$cur     = date( 'N' ) - 1;
		$between = $cur * 24 * 3600;
		$time    = time() - $between;
		$time    = strtotime( date( 'Y-m-d', $time ) . " 00:00:00" );

		$this->redis->set( 'roomrankWeekUtimestamp', "$time" );
	}

	function getWRankUTimestamp()
	{
		return (int)$this->redis->get( 'roomrankWeekUtimestamp' );
	}

	function delRankDayList()
	{
		$key = $this->pre . ":day";
		$this->redis->zRemRangeByRank( $key, 0, -1 );
	}

	function delRankWeekList()
	{
		$key = $this->pre . ":week";
		$this->redis->zRemRangeByRank( $key, 0, -1 );
	}

	function intoRankList( $uid, $cost )
	{
		if ( $this->intoDayList( $uid, $cost ) && $this->intoWeekList( $uid, $cost ) && $this->intoAllList( $uid, $cost ) )
		{
			return true;
		}

		return false;
	}

	function intoDayList( $uid, $cost )
	{

		$time = date( 'Y-m-d' );
		$sql  = "insert into rank_day (`date`, uid, luid, cost) VALUE ('$time', $uid, {$this->luid}, $cost) on duplicate key update cost = cost+$cost";
		if ( !$this->db->query( $sql ) )
		{
			return false;
		}

		if ( $this->isDayListChange( $uid, $cost ) )
		{
			$content = array(
				"t"    => '701',
				'type' => 1
			);
			$this->lroom->sendRoomMsg( json_encode( toString( $content ) ) );
		}

		return true;
	}

	function intoWeekList( $uid, $cost )
	{
		$cur     = date( 'N' ) - 1;
		$between = $cur * 24 * 3600;
		$time    = time() - $between;
		$time    = date( "Y-m-d", $time );

		$sql = "insert into rank_week (`date`, uid, luid, cost) VALUE ('$time', $uid, {$this->luid}, $cost) on duplicate key update cost = cost+$cost";
		if ( !$this->db->query( $sql ) )
		{
			return false;
		}

		if ( $this->isWeekListChange( $uid, $cost ) )
		{
			$content = array(
				"t"    => '701',
				'type' => 2
			);
			$this->lroom->sendRoomMsg( json_encode( toString( $content ) ) );
		}

		return true;
	}

	function intoAllList( $uid, $cost )
	{
		$sql = "insert into rank_all ( uid, luid, cost) VALUE ($uid, {$this->luid}, $cost) on duplicate key update cost = cost+$cost";
		if ( !$this->db->query( $sql ) )
		{
			return false;
		}

		if ( $this->isAllListChange( $uid ) )
		{
			$content = array(
				"t"    => '701',
				'type' => 3
			);
			$this->lroom->sendRoomMsg( json_encode( toString( $content ) ) );
		}

		return true;
	}

	function isDayListChange( $uid, $cost )
	{
		$daylist = $this->redis->zRevRange( $this->rankDay, 0, 9 );
		if ( !$daylist || count( $daylist ) == 0 )
		{
			$this->redis->zadd( $this->rankDay, (int)$cost, "$uid" );

			return true;
		}
		$isChange = false;
		$tmpScore = (int)$this->redis->zRank( $this->rankDay, "$uid" ) + $cost;
		if ( count( $daylist ) >= 10 )
		{
			if ( in_array( "$uid", $daylist ) )
			{
				//当前用户在前十名
				$index = array_search( "$uid", $daylist );
				if ( $index === 0 )
				{
					//return false;
					//有分数显示，在排行榜内都应该变化
					$isChange = true;
				}
				else
				{
					if ( $tmpScore > $this->getDayScore( $daylist[$index - 1] ) )
					{
						//排名有变化
						$isChange = true;
					}
					else
					{
						//排名无变化
						$isChange = true;
					}
				}
			}
			else
			{
				//当前用户未上榜
				if ( $tmpScore > $this->getDayScore( $daylist[9] ) )
				{
					//排名有变化
					$isChange = true;
				}
				else
				{
					//排名无变化
					$isChange = false;
				}
			}
		}
		else
		{
			if ( in_array( "$uid", $daylist ) )
			{
				$index = array_search( "$uid", $daylist );
				if ( $index === 0 )
				{
					$isChange = true;
				}
				else
				{
					if ( $tmpScore > $this->getDayScore( $daylist[$index - 1] ) )
					{
						$isChange = true;
					}
					else
					{
						$isChange = true;
					}
				}
			}
			else
			{
				$isChange = true;
			}
		}
		$this->redis->zadd( $this->rankDay, $tmpScore, "$uid" );

		return $isChange;

	}

	function isWeekListChange( $uid, $cost )
	{
		$weeklist = $this->redis->zRevRange( $this->rankWeek, 0, 9 );
		$tmpScore = (int)$this->redis->zRank( $this->rankWeek, "$uid" ) + $cost;
		$count    = count( $weeklist );
		if ( !$weeklist || $count == 0 )
		{
			$this->redis->zadd( $this->rankWeek, $cost, "$uid" );

			return true;
		}
		$isChange = false;
		if ( $count >= 10 )
		{
			if ( in_array( "$uid", $weeklist ) )
			{
				$index = array_search( "$uid", $weeklist );
				if ( $index === 0 )
				{
					$isChange = true;
				}
				else
				{
					if ( $tmpScore > $this->getWeekScore( $weeklist[$index - 1] ) )
					{
						$isChange = true;
					}
					else
					{
						$isChange = true;
					}
				}
			}
			else
			{
				if ( $tmpScore > $this->getWeekScore( $weeklist[9] ) )
				{
					$isChange = true;
				}
			}
		}
		else
		{
			if ( in_array( "$uid", $weeklist ) )
			{
				$index = array_search( "$uid", $weeklist );
				if ( $index === 0 )
				{
					$isChange = true;
				}
				else
				{
					$isChange = true;
				}
			}
			else
			{
				$isChange = true;
			}
		}
		$this->redis->zadd( $this->rankWeek, $tmpScore, "$uid" );

		return $isChange;
	}

	function isAllListChange( $uid )
	{
		$allList  = $this->redis->zRevRange( $this->rankAll, 0, -1 );
		$tmpScore = $this->getAllScore( $uid );
		$count    = count( $allList );

		if ( !$allList || $count == 0 )
		{
			$this->redis->zadd( $this->rankAll, $tmpScore, "$uid" );

			return true;
		}

		$isChange = false;
		if ( $count >= 10 )
		{
			if ( in_array( "$uid", $allList ) )
			{
				$this->redis->zadd( $this->rankAll, $tmpScore, "$uid" );

				return true;
			}
			else
			{
				if ( $tmpScore > $this->getAllScore( $allList[9] ) )
				{
					$this->redis->zadd( $this->rankAll, $tmpScore, "$uid" );
					$this->redis->zRemRangeByRank( $this->rankAll, 10, -1 );

					return true;
				}
			}
		}
		else
		{
			$this->redis->zadd( $this->rankAll, $tmpScore, "$uid" );

			return true;
		}

		return $isChange;

	}

	function getDayScore( $member )
	{
		return (int)$this->redis->zRank( $this->rankDay, $member );
	}

	function getWeekScore( $member )
	{
		return (int)$this->redis->zRank( $this->rankWeek, $member );
	}

	function getAllScore( $uid )
	{
		$luid = $this->luid;
		$sql  = "select cost from rank_all where uid=$uid and luid=$luid";
		$res  = $this->db->query( $sql );
		$row  = $res->fetch_assoc();

		return (int)$row['cost'];
	}
}

?>