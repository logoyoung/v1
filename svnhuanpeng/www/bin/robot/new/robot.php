<?php
/**
 * Created by PhpStorm.
 * User: hantong
 * Date: 17/5/16
 * Time: 下午10:56
 */


include __DIR__ . "/../../../include/init.php";

error_reporting( E_ALL & ~E_NOTICE );
use lib\room\RobotActive;
use lib\LiveActivity;

class RobotAdd
{

	const SLEEP_RANGE = [ 5, 10 ];

	const ROBOT_NUMBER_INTERVAL = [ 1, 4 ];

	const ROBOT_ACTIVE_INTERVAL = [ 4, 7 ];

	const CLEAR_TIMER = 3;

	const ROOM_MAX_ROBOT_COUNT = 20;

	private $_db;
	private $_redis;
	private $_activeObj;

	private $_viewerInterval;


	private $_activeInterval;

	private $_robotInterval;

	private $_roomRobotList = [];

	private $_allRobotInfo = [];

	private $_allRobotList = [];

	private $_msgList = [];

	public function __construct( \DBHelperi_huanpeng $db, \RedisHelp $redisHelp )
	{
		if ( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new DBHelperi_huanpeng();
		}


		if ( $redisHelp )
		{
			$this->_redis = $redisHelp;
		}
		else
		{
			$this->_redis = new RedisHelp();
		}

		$this->_activeObj = new RobotActive( $this->_db, $this->_redis );

		$this->_activeInterval = $this->_viewerInterval = [];


		$this->_msgList = array( "666666666", "666", "233333333", "233", "FFFFFFFFFF", "主播，好有爱！", "求露脸～", "不要逗！", "主播，求BGM", "约吗？", "吓死宝宝了！", "我的内心几乎是崩溃的", "笑尿了", "演的不错", "漂亮", "啊啊啊", "哈哈哈啊啊", "哈哈", "呵呵", "完美", "套路", "٩( 'ω' )و", "0.0", "？？？", "。。。", "太简单了吧", "←_←", "→_→", "厉害了!", "那你很棒哦！", "尴尬", "还能这么玩", "困=_=", "看不懂啊", "好冷", "欢迎", "我服了", "服了", "没办法", "别闹了", "醉了", "来晚了。。", "给主播来波豆", "可以的", "关注了", "你好意思吗？" );

		for ( $i = 1; $i <= 22; $i++ )
		{
			array_push( $this->_msgList, str_repeat( "[em_$i]", rand( 2, 5 ) ) );
		}


		$this->_logDir = __CLASS__ . 'log';

	}

	public function ruleFictitiousViewer( $viewerCount )
	{
		//[0,10] 2倍，[10,20] 三倍，[20,30] 4倍, [30,40] 5倍，[40,+...] 6倍
		$ruleTable = [
			'10'   => 50,
			'50'   => 55,
			'100'  => 60,
			'500'  => 65,
			'1000' => 70,
			'-1'   => 75
		];

		foreach ( $ruleTable as $key => $value )
		{
			if ( $key == -1 )
			{
				return $viewerCount * $value + rand( 1, 40 );
			}
			else
			{
				if ( $viewerCount < $key )
				{
					return $viewerCount * $value + rand( 1, $key - 1 );
				}
			}
		}
	}

	public function getSleepTime()
	{
		return call_user_func_array( 'rand', self::SLEEP_RANGE );
	}

	public function getViewerInterval()
	{
		return call_user_func_array( 'rand', self::ROBOT_NUMBER_INTERVAL );
	}

	public function getActiveInterval()
	{
		return call_user_func_array( 'rand', self::ROBOT_ACTIVE_INTERVAL );
	}


	public function getChatMsg()
	{
		$msgIndex = rand( 0, count( $this->_msgList ) - 1 );
		$msg      = $this->_msgList[$msgIndex];

		return $msg;
	}

	public function getNewRobot()
	{
		$this->initRobotInfo();

		$robotIndex = rand( 0, count( $this->_allRobotList ) - 1 );

		return $this->_allRobotList[$robotIndex];
	}

	public function getRobotInfo( $uid )
	{
		$this->initRobotInfo();
		$result = $this->_allRobotInfo[$uid];

		return $result;
	}

	public function initRobotInfo()
	{
		if ( !$this->_allRobotInfo )
		{
			$result = array();

			$sql = "select nick,pic,userstatic.uid as uid,`level` from userstatic,useractive where username='hpRobot' and userstatic.uid=useractive.uid";
			$res = $this->_db->query( $sql );
			while ( $row = $res->fetch_assoc() )
			{
				$result[$row['uid']]['uid']   = $row['uid'];
				$result[$row['uid']]['nick']  = $row['nick'];
				$result[$row['uid']]['pic']   = $row['pic'];
				$result[$row['uid']]['level'] = $row['level'];
			}

			$this->_allRobotInfo = $result;
			$this->_allRobotList = array_keys( $result );
		}
	}

	public function run()
	{

		while ( true )
		{
			// $list = LiveActivity::getLiveUids( $this->_db );
			$list = \service\live\LiveService::getLivingLuidByType();

			$this->_log( __FUNCTION__ . "::" . "current live uid list " . json_encode( $list ) );

			foreach ( $list as $luid )
			{
				if ( !isset( $this->_viewerInterval[$luid] ) )
				{
					$this->reset( $luid );
				}

				if ( !isset( $this->_activeInterval[$luid] ) )
				{
					$this->robotReset( $luid );
				}
			}

			$this->viewRun();
			$this->robotRun();

//			$stime = microtime(true);

//			$runtimeLog = [
//				'rumtime'=> microtime(true) - $stime,
//				'foreachCount' => count($this->_viewerInterval)
//			];
//			$this->_log(json_encode($runtimeLog));

			$sleep = $this->getSleepTime();
			$this->_log( __FUNCTION__ . "::" . json_encode( [ 'sleeptime' => $sleep ] ) );
			sleep( $sleep );
		}
	}

	public function viewRun()
	{

		foreach ( $this->_viewerInterval as $luid => $interval )
		{
//			$runTime = $this->_anchorViewerInterval[$luid];
			$runTime  = $interval['runTime'];
			$interval = $interval['interval'];

			// $this->_log( __FUNCTION__ . "::" . json_encode( [ 'userid' => $luid, 'interval' => $interval, 'runtime' => $runTime ] ) );

			if ( $runTime < $interval )
			{

				$this->_viewerInterval[$luid]['runTime']++;
			}
			else
			{
				$roomObj = $this->_activeObj->getLiveRoomObj( $luid );

				$viewerCount = $roomObj->getLiveRoomUserCount();
				$fictitious  = $roomObj->getLiveRoomUserCountFictitious();

				if ( $viewerCount == 0 )
				{
					$viewer = $fictitious;

					$this->addToClearClock( $luid );

				}
				else
				{
					$viewer = $this->ruleFictitiousViewer( $viewerCount );
					$this->rmFromClearClock( $luid );

				}

				// $this->_log( __FUNCTION__ . "::" . json_encode( [ 'userid' => $luid, 'realViewer' => $viewerCount, 'fictitious' => $viewer ] ) );


				$md = $viewer - $fictitious;

				if ( $md > 0 )
				{
					$roomObj->addFictitiousViewCount( $md );
				}
				else
				{
					$roomObj->subFictitiousViewCount( $md );
				}

				if ( $md != 0 )
				{
					$this->_activeObj->upDateRoomViewerMsg( $luid );
				}

				if ( $this->isCanClear( $luid ) )
				{
					$this->_log( __FUNCTION__ . "::" . json_encode( [ 'clearUser' => $luid ] ) );

					$this->clear( $luid );
				}
				else
				{
					$this->reset( $luid );
				}
			}
		}
	}

	public function robotRun()
	{
		foreach ( $this->_activeInterval as $luid => $interval )
		{

			$runTime  = $interval['runTime'];
			$interval = $interval['interval'];

			// $this->_log(__FUNCTION__."::".json_encode(['userid'=>$luid, 'interval'=>$interval,'runtime'=>$runTime]));

			if ( $runTime < $interval )
			{
				$this->_activeInterval[$luid]['runTime']++;
			}
			else
			{
				$robotCount = count( $this->_roomRobotList[$luid] );
				// $this->_log(__FUNCTION__."::".json_encode(['userid'=>$luid,'robotCount'=>$robotCount, 'list'=>$this->_roomRobotList[$luid]]));
				if ( !$robotCount )
				{
					// $this->_log(__FUNCTION__."::"."create robot");
					$this->robotEnter( $luid );
				}
				elseif ( $robotCount > self::ROOM_MAX_ROBOT_COUNT )//先按照每个房间20个假人设计
				{
					// $this->_log(__FUNCTION__."::"."room robot max and level");
					$this->robotExit( $luid );
				}
				else
				{
					if ( rand( 1, 10 ) > 6 )
					{
						// $this->_log(__FUNCTION__."::"."robot msg");
						$this->robotMsg( $luid );
					}
					else
					{
						// $this->_log(__FUNCTION__."::"."robot active enter room or level");
						$this->robotEnterOrExit( $luid );
					}
				}

				$this->robotReset( $luid );
			}
		}
	}

	public function robotMsg( $luid )
	{
		$uid = $this->getRoomOneRobot( $luid );
		if ( $uid )
		{
			$info = $this->getRobotInfo( $uid );
			$msg  = $this->getChatMsg();
			$this->_activeObj->msg( $luid, $info, $msg );
		}
	}

	public function robotEnterOrExit( $luid )
	{
		if ( rand( 1, 10 ) > 8 )
		{
			$this->robotExit( $luid );
		}
		else
		{
			$this->robotEnter( $luid );
		}
	}

	public function robotEnter( $luid )
	{
		$uid  = $this->getNewRobot();
		$info = $this->getRobotInfo( $uid );

		$this->addRobot( $luid, [ $uid ] );
		$this->_activeObj->enterRoom( $luid, $info );
	}

	public function robotExit( $luid )
	{
		$uid = $this->getRoomOneRobot( $luid );

		if ( $uid )
		{
			$info = $this->getRobotInfo( $uid );

			$this->subRobot( $luid, [ $uid ] );
			$this->_activeObj->exitRoom( $luid, $info );
		}
	}

	public function getRoomOneRobot( $luid )
	{
		$robotCount = count( $this->_roomRobotList[$luid] );

		if ( $robotCount )
		{

			if ( $robotCount == 1 )
			{
				$index = 0;
			}
			else
			{
				$index = rand( 0, $robotCount - 1 );

			}

			$uid = $this->_roomRobotList[$luid][$index];

			return $uid;
		}

		return false;
	}

	public function addRobot( $luid, $uids )
	{

		foreach ( $uids as $uid )
		{

			if ( !is_array( $this->_roomRobotList[$luid] ) )
			{
				$this->_roomRobotList[$luid] = [];
			}

			if ( !in_array( $uid, $this->_roomRobotList[$luid] ) )
			{
				array_push( $this->_roomRobotList[$luid], $uid );
			}
		}

		//todo 优化
		$key = $this->getRobotHeadListKey( $luid );

		$this->_redis->del( $key );
		$this->_redis->getMyRedis()->sAddArray( $key, $this->_roomRobotList[$luid] );
	}

	public function subRobot( $luid, $uids )
	{
		foreach ( $uids as $uid )
		{
			if ( !is_array( $this->_roomRobotList[$luid] ) )
			{
				$this->_roomRobotList[$luid] = [];
			}

			if ( in_array( $uid, $this->_roomRobotList[$luid] ) )
			{
				$index = array_search( $uid, $this->_roomRobotList );
				unset( $this->_roomRobotList[$luid][$index] );
			}
		}

		asort( $this->_roomRobotList[$luid] );
		//todo 优化
		$key = $this->getRobotHeadListKey( $luid );

		$this->_redis->del( $key );
		$this->_redis->getMyRedis()->sAddArray( $key, $this->_roomRobotList[$luid] );
	}

	public function getRobotHeadListKey( $luid )
	{
		return $GLOBALS['env'] . "_robotHeadList" . $luid;
	}

	public function clear( $luid )
	{
		unset( $this->_viewerInterval[$luid] );
		//清空房间机器人
		$count = count( $this->_roomRobotList[$luid] ) - 1;

		for ( $i = 0; $i < $count; $i++ )
		{
			$this->robotExit( $luid );
		}

		unset($this->_roomRobotList[$luid]);
		unset($this->_robotInterval[$luid]);
	}

	public function reset( $luid )
	{
		$this->_viewerInterval[$luid]['interval'] = $this->getViewerInterval();
		$this->_viewerInterval[$luid]['runTime']  = 0;

	}

	public function robotReset( $luid )
	{
		$this->_activeInterval[$luid]['interval'] = $this->getActiveInterval();
		$this->_activeInterval[$luid]['runTime']  = 0;
		//send msg interval
		//enter room interval
//		$this->_roomRobotList[$luid] = [];
	}

	public function isCanClear( $luid )
	{
		if ( isset( $this->_viewerInterval[$luid]['clock'] ) && $this->_viewerInterval[$luid]['clock'] >= self::CLEAR_TIMER )
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function addToClearClock( $luid )
	{
		if ( isset( $this->_viewerInterval[$luid]['clock'] ) )
		{
			$this->_viewerInterval[$luid]['clock']++;
		}
		else
		{
			$this->_viewerInterval[$luid]['clock'] = 1;
		}

		// $this->_log( __FUNCTION__ . json_encode( [ 'userid' => $luid, 'clock' => $this->_viewerInterval[$luid]['clock'] ] ) );
	}

	public function rmFromClearClock( $luid )
	{
		if ( isset( $this->_viewerInterval[$luid] ) )
		{
			$this->_viewerInterval[$luid]['clock'] = 0;
		}
	}


	private function _log( $msg )
	{
		$logname = __CLASS__ . "log";
		write_log( $msg, $logname );
	}
}


$db       = new \DBHelperi_huanpeng();
$redis    = new \RedisHelp();
$robotObj = new RobotAdd( $db, $redis );

$robotObj->run();

