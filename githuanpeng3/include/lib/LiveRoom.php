<?php
namespace lib;


use DBHelperi_huanpeng;
use RedisHelp;
use service\pack\BackpackService;
use service\robot\RoomRobotService;
use service\rule\TextService;
use service\event\EventManager;
use service\user\UserAuthService;

//use lib\MsgPackage;
//use lib\RoomRank;
//use lib\SocketSend;
//use \PickBean;
//use lib\Anchor;
//use lib\User;
//use lib\Gift;
//use lib\RedisCacheManage;
//use lib\RoomTimerGetBean;
//use lib\TreasureBox;
//use lib\LivePush;

/**
 * 房间操作类文件
 */

/**
 * 房间类
 *
 * Class LiveRoom
 *
 * @package hp\lib
 */
class LiveRoom
{

	const REDIS_KEY_LIVE_STATISTIC_INFO = "LIVE_STATISTIC_INFO";

	const REDIS_HFIELD_LIVE_STATISTIC = 'info';

	const REDIS_KEY_LIVE_USER_COUNT = "HASH_LIVE_USER_COUNT";

	const REDIS_HFIELD_LIVE_USER_COUNT_PEAK = "peak";

	const REDIS_HFIELD_LIVE_USER_COUNT_PEAK_FICTITIOUS = 'peakFictitious';

	const REDIS_HFIELD_LIVE_USER_COUNT = 'count';

	const REDIS_HFIELD_LIVE_USER_COUNT_FICTITIOUS = 'countFictitious';

	const REDIS_KEY_LIVE_GIFT_TIMER = "LIVE_GIFT_TIMER";

	const REDIS_KEY_LIVE_GIFT_TIMER_KEY_LIST = "LIVE_GIFT_TIMER_KEY_LIST";

	const TABLE_LIVE_ROOM = 'liveroom';

	const TABLE_LIVE_USER_MESSAGE = 'livemsg';


	const REDIS_KEY_ROOM_USER_HASH = "LIVEROOM";

	const REDIS_KEY_ROOM_USER_LIST = "ROOM:";

	const ROOM_USER_FIELD_LENGTH = 6;

	const SHOW_WEL_LEVEL_BASE_LINE = 1;

	const TREASURE_TIME_OUT = TREASURE_TIME_OUT;

	const SILENCED_KEY = 'JR*&_+23d10~`9|9)diuy';

	const SILENCED_URL = ADMIN_HOST_URL . "admin2/api/user/silenceAdd.php";

	const BACK_SUCCESS_STAT = 1;


	const SEND_BEAN_ALLOW_LIST = [ 50, 100, 200, 520, 666, 888, 999, 1000, 1314 ];

	/**
	 * @var int 主播ID
	 */
	private $_luid;

	private $_roomid;

	/**
	 * @var DBHelperi_huanpeng 数据库实例
	 */
	private $_db;

	/**
	 * @var RedisHelp redis 实例
	 */
	private $_redis;

	/**
	 * @var bool debug 模式
	 */
	private $_debug = false;

	private $_rankObj;

	private $_anchorObj;

	/**
	 * @var bool 是否为test模式
	 */
	private $_isTest = false;

	/**
	 * @var array 白名单列表，用于测试
	 */
	private $_whiteList = array();

	/**
	 * LiveRoom constructor.
	 *
	 * @param                         $luid    主播ID
	 * @param DBHelperi_huanpeng|null $db      数据库实例化
	 * @param RedisHelp|null          $redis   redis实例化
	 * @param bool                    $debug   调试模式
	 */
	public function __construct( $luid, DBHelperi_huanpeng $db = null, RedisHelp $redis = null, $debug = false )
	{
		$this->_luid = (int)$luid;
		if ( !$this->_luid )
		{
			return false;
		}
		if ( $db )
		{
			$this->_db = $db;
		}
		else
		{
			$this->_db = new DBHelperi_huanpeng();
		}

		if ( $redis )
		{
			$this->_redis = $redis;
		}
		else
		{
			$this->_redis = new RedisHelp();
		}

		$this->_debug     = $debug;
		$this->_debug     = true;
		$this->_whiteList = explode( ',', WHITE_LIST );
		$this->_anchorObj = new Anchor( $luid, $db );
		$this->_roomid    = $this->_anchorObj->getRoomID();
		$this->_rankObj   = new RoomRank( $this->_luid, $this->_db, $this->_redis );

	}

	/**
	 * 用户进入房间
	 *
	 * @param int    $uid        用户ID
	 * @param string $useraddr   用户ip port 122.70.112.11:1231
	 * @param string $serveraddr 服务器 IP port 122.70.112.11:1231
	 *
	 * @return bool
	 */
	public function userEnter( int $uid, string $useraddr, string $serveraddr )
	{
		list( $userip, $userport ) = explode( ':', $useraddr );
		list( $serverip, $serverport ) = explode( ':', $serveraddr );
		$serverip = ip2long( $serverip );
		$userip   = ip2long( $userip );

		// 取用户昵称
		$user = $this->_getUser( $uid );

		if ( !$user )
		{
			$this->_log( __FUNCTION__ . "uid : $uid can't get user info" );

			return false;
		}

		$data = array(
			'luid'       => $this->_luid,
			'uid'        => $uid,
			'userip'     => $userip,
			'userport'   => $userport,
			'serverip'   => $serverip,
			'serverport' => $serverport
		);

		$update = array(
			'serverip'   => $serverip,
			'serverport' => $serverport
		);

		// 写在线表
		$sql = $this->_db->insertDuplicate( 'liveroom', $data, $update, true );
		$this->_log( $sql );
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			$t = "QueryError @ LiveRoom::userEnter($uid, $serveraddr)[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			return false;
		}

		$this->_userEnterRecordByRedis( $uid );
		$this->_addFictitiousViewCount();

		$this->setLiveRoomUserCountPeakValue();


		// 用户是游客，进入不通知
		if ( $uid >= LIVEROOM_ANONYMOUS )
		{
			return true;
		}

		// 写浏览历史表
		if ( $this->_luid != PUSH_ROOM_ID )
		{
			$stime = date( "Y-m-d H:i:s" );

			$data = array(
				'uid'   => $uid,
				'luid'  => $this->_luid,
				'stime' => $stime
			);

			$update = array( 'stime' => $stime );

			$sql = $this->_db->insertDuplicate( 'history', $data, $update, true );

			$res = $this->_db->query( $sql );
			if ( !$res )
			{
				$t = "QueryError @ LiveRoom::userEnter($uid, $serveraddr)[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
				mylog( $t );

				return false;
			}
		}

		return true;
	}

	/**
	 *用户成功进入房间
	 *
	 * 用于防止用户进入房间收取不到socket消息
	 *
	 * @param int   $uid 用户ID
	 * @param array $arr 附带数据
	 *
	 * @return bool
	 */
	public function successEnter( int $uid, array $arr )
	{
		$viewCount = $this->getLiveRoomUserCountFictitious();
		$e         = 0;

		if ( $uid >= LIVEROOM_ANONYMOUS )
		{
			$nick     = '';
			$showHead = 0;
			$showWel  = 0;
			$isGust   = 0;
			$level    = 1;
			$pic      = "";
			$group    = 1;
		}
		else
		{
			$userInfo = $this->_getUser( $uid );

			if ( !$userInfo )
			{
				return fasle;
			}

			$nick     = $userInfo['nick'];
			$level    = (int)$userInfo['level'];
			$pic      = $userInfo['pic'];
			$group    = $this->getUsergroup( $uid );
			$isGust   = 0;
			$showHead = 1;
			$showWel  = $this->isShowWel( $level, $uid );
		}

		$UserEnterMsgPackage = MsgPackage::getUserEnterMsgSocketPackage( $this->_luid, $uid, $nick, $group, $level, $pic, $viewCount, $showHead, $showWel, $isGust );
		$this->_sendSocketMsg( $UserEnterMsgPackage );

		$mid = intval( $arr['mid'] ) ? intval( $arr['mid'] ) : time();

		$enterCallBackMsgPackage = MsgPackage::getUserSuccEnterMsgCallBackSocketpackage( $this->_luid, $uid, $mid, $e );
		$this->_sendSocketMsg( $enterCallBackMsgPackage );

		return true;
	}

	/**
	 * 用户断开连接，或者退出房间
	 *
	 * @param int    $uid  用户ID
	 * @param string $addr 用户IP:port
	 *
	 * @return bool
	 */
	public function userExit( int $uid, string $addr )
	{
		$this->_log( __FUNCTION__ );
		list( $ip, $port ) = explode( ':', $addr );
		$ip = ip2long( $ip );

		$sql = "DELETE FROM " . self::TABLE_LIVE_ROOM . " WHERE luid = {$this->_luid} AND uid=$uid and userport=$port and userip=$ip";
		$res = $this->_db->query( $sql );

		if ( !$res )
		{
			$t = "QueryError @ LiveRoom::userExit($uid)[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			return false;
		}

		$this->_userExitRecordByRedis( $uid );
		$this->_subFictitiousViewCount();


		$viewCount = $this->getLiveRoomUserCountFictitious();

		if ( $uid >= LIVEROOM_ANONYMOUS )
		{
			$showHead = 0;
			$showWel  = 0;
			$isGust   = 1;
		}
		else
		{
			$showHead = 1;
			$showWel  = 1;
			$isGust   = 0;

			$timerGetBean = new RoomTimerGetBean( $uid, $this->_db, $this->_redis );
			$timerGetBean->exitRoom( $this->_luid );

//			$pick = new \PickBean( $uid, $this->_db );
//			$pick->exitRoom( $this->_luid );
		}

		$userExitMsgPackage = MsgPackage::getUSerExitMsgSocketPackage( $this->_luid, $uid, $viewCount, $showHead, $showWel, $isGust );

		return $this->_sendSocketMsg( $userExitMsgPackage );
	}

	/**
	 * redis 房间列表 用户进入房间
	 *
	 * @param int $uid
	 */
	private function _userEnterRecordByRedis( int $uid )
	{
		$field            = $this->_getUserField( $uid );
		$userListRedisKey = $this->_getUserListRedisKey( $uid );

		$this->_redis->hset( self::REDIS_KEY_ROOM_USER_HASH, $field, $userListRedisKey );
		$this->_redis->sadd( $userListRedisKey, $uid );
	}

	/**
	 * redis 房间列表 用户退出
	 *
	 * @param int $uid
	 */
	private function _userExitRecordByRedis( int $uid )
	{
		$this->_log( __FUNCTION__ );
		if ( !$this->isInLiveRoom( $uid ) )
		{
			$this->_log( "not in room" );
			$field = $this->_getUserField( $uid );
			$key   = $this->_redis->hget( self::REDIS_KEY_ROOM_USER_HASH, $field );
			$this->_log( $key );
			if ( $key )
			{
				$result = $this->_redis->sRem( $key, $uid );
				$this->_log( $result );
			}
		}
	}

	/**
	 * 获取用户房间在线hash列表的field
	 *
	 * @param int $uid
	 *
	 * @return string
	 */
	private function _getUserField( int $uid )
	{
		$index = $uid % self::ROOM_USER_FIELD_LENGTH;

		return $this->_getRedisField( $this->_luid, $index );
	}

	/**
	 * 获取用户房间在线列表的redis key
	 *
	 * @param int $uid
	 *
	 * @return string
	 */
	private function _getUserListRedisKey( int $uid )
	{
		return self::REDIS_KEY_ROOM_USER_LIST . $this->_getUserField( $uid );
	}

	/**
	 * 检测用户是否在房间内
	 *
	 * @param int $uid
	 *
	 * @return int
	 */
	public function isInLiveRoom( int $uid )
	{
		$sql = "select count(uid) as total from " . self::TABLE_LIVE_ROOM . " where uid=$uid and luid={$this->_luid}";
		$res = $this->_db->query( $sql );
		$row = $res->fetch_assoc();

		$total = $row ? (int)$row['total'] : 0;

		return $total;
	}

	/**
	 * 更新用户在线状态(心跳)
	 *
	 * @param int $uid 用户ID
	 *
	 * @return bool
	 */
	public function userHB( int $uid )
	{
		$sql = "UPDATE liveroom SET tm=now() WHERE luid={$this->_luid} AND uid=$uid";
		$res = $this->_db->query( $sql );
		if ( !$res )
		{
			$t = "QueryError @ LiveRoom::userHB($uid)[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			mylog( $t );

			return false;
		}

		return true;
	}

	/**
	 * 用户发言
	 *
	 * @param int    $uid        用户ID
	 * @param array  $params     附带参数包含发言信息
	 * @param string $useraddr   用户地址ip:port
	 * @param string $serveraddr 服务器地址ip:port
	 *
	 * @return bool
	 */
	public function userMsg( int $uid, array $params, string $useraddr, string $serveraddr )
	{
		// TODO: userMsg methods
		$errno   = 0;
		$msg     = $params['msg'];
		$mid     = 0;
		$isPhone = isset( $params['way'] ) ? (int)$params['way'] : 0;

		if ( $uid > LIVEROOM_ANONYMOUS )
		{
			roomerror( -3008 );
		}

		if ( $this->isSilenced( $uid ) )
		{
			$errno = -3009;
		}

		if ( !$errno )
		{
			$errno = $this->_checkMessageIsValid( $msg );
		}

		if ( !$errno )
		{
			$errno = $this->_checkMessageIsLawful( $msg, $uid );
		}

		if ( !$errno )
		{
			$mid      = (int)$params['mid'];
			$userInfo = $this->_getUser( $uid );
			if ( !$userInfo )
			{
				$errno = -3503;
			}
			else
			{
				$nick  = $userInfo['nick'];
				$group = $this->getUserGroup( $uid );
				$level = $userInfo['level'];

			}
		}

		if ( !$errno )
		{
			$msgID = $this->_intoMsgListTable( $uid, $params, $useraddr, $serveraddr );
			if ( !$msgID )
			{
				$errno = -3501;
			}
		}

		if ( !$errno )
		{
			$userMsgPackage = MsgPackage::getUserMsgSocketPackage( $this->_luid, $uid, $nick, $msg, $group, $level, $isPhone, $msgID );
			$r              = $this->_sendSocketMsg( $userMsgPackage );
			if ( !$r )
			{
				$errno = -3502;
			}
		}

		$userMsgCallBackPackage = MsgPackage::getUserMsgCallBackSocketPackage( $this->_luid, $uid, $mid, $errno );

		return $this->_sendSocketMsg( $userMsgCallBackPackage );
	}

	/**
	 *
	 *
	 * @param int $uid
	 *
	 * @return bool
	 */
	public function isSilenced( int $uid )
	{
		$auth = new UserAuthService();
		$auth->setUid( $uid );
		$auth->setAnchorUid( $this->_luid );

		$silencedStatus = $auth->checkSilencedStatus();

		if ( $silencedStatus === false )
		{
			$result = $auth->getResult();
			$etime  = $result['silenced_etime'];

			return $etime;
		}

		return false;

//		//todo 禁言流程
//		$roomid = $this->_anchorObj->getRoomID();
//		if ( !$this->_roomid )
//		{
//			return false;
//		}
//		$roomSilencedRedisKey = "silence_" . $this->_roomid;
//		$allSilencedRedisKey  = "silence_0";
//
//		$keyList = [ $allSilencedRedisKey, $roomSilencedRedisKey ];
//
//
//		foreach ( $keyList as $redisKey )
//		{
//			$silencedInfo = $this->_redis->hget( $redisKey, $uid );
//			if ( $silencedInfo )
//			{
//				$timeLimit = json_decode( $silencedInfo, true );
//
//				if ( $timeLimit )
//				{
//					$len = time() - $timeLimit['etime'];
//					if ( $len >= 0 )
//					{
//						$this->_redis->hdel( $redisKey, $uid );
//					}
//					else
//					{
//						return $timeLimit['etime'];
//					}
//				}
//				else
//				{
//					//TODO 解析出错，去后台去取来验证
//				}
//			}
//		}
//
//		return false;
	}

	/**
	 * @param int    $targetUid
	 * @param string $targetNick
	 * @param int    $optUid
	 * @param string $optNick
	 * @param int    $timeLength
	 * @param string $reason
	 * @param bool   $roomid
	 *
	 * @return bool
	 */
	public function setSilenced( int $targetUid, string $targetNick, int $optUid, string $optNick, $timeLength = 0, $reason = '', $roomid = false )
	{
		//todo just send msg
		$this->_log( __FUNCTION__ . "禁言时间长度 $timeLength" );

//		$data         = [
//			'uid'        => $optUid,
//			'luid'       => $targetUid,
//			'timeLength' => $timeLength,
//			'reason'     => $reason,
//			'roomid'     => $roomid === false ? $this->_roomid : $roomid,
//			'tm'         => time()
//		];
//		$sign         = buildSign( $data, self::SILENCED_KEY );
//		$data['sign'] = $sign;
//
//		$ret = curl_post( $data, self::SILENCED_URL );
//		$ret = json_decode( $ret, true );

//		if ( $ret && $ret['stat'] == self::BACK_SUCCESS_STAT )
//		{
		$outTime = time() + $timeLength;//(int)$ret['resuData']['etime'];
		$group   = $this->getUserGroup( $optUid );
		$timeStr = $this->_getTimeStr( $timeLength );

		$msgPackage = MsgPackage::getRoomSilenceUserMsgSocketPackage( $this->_luid, $targetUid, $targetNick, $optUid, $optNick, $group, $outTime, $timeStr );
		$this->_sendSocketMsg( $msgPackage );

		return true;
//		}
//		else
//		{
//			return false;
//		}
	}

//	public function getTimeStr($timeLength)
//	{
//		return $this->_getTimeStr($timeLength);
//	}

	private function _getTimeStr( $timeLength )
	{
		if ( $timeLength == 0 )
		{
			return "永久";
		}

		$startTime = time();
		$endTime   = $startTime + $timeLength;

		$conf = [
			[
				'param' => 'j',
				'str'   => "天"
			],
			[
				'param' => 'G',
				'str'   => "小时"
			],
			[
				'param' => 'i',
				'str'   => '分钟'
			]
		];

		foreach ( $conf as $oneTimeConf )
		{
			$param = $oneTimeConf['param'];
			$str   = $oneTimeConf['str'];

			if ( $param == 'j' )
			{
				$between = intval( $timeLength / ( 24 * 3600 ) );
			}
			elseif ( $param == 'G' )
			{
				$between = intval( $timeLength / 3600 );
			}
			elseif ( $param == 'i' )
			{
				$between = intval( $timeLength / 60 );
			}

			if ( $between > 0 )
			{
				return $between . $str;
			}
		}
	}

	/**
	 * 检测msg是否有效
	 *
	 * @param $msg
	 *
	 * @return int
	 */
	private function _checkMessageIsValid( &$msg )
	{
		$msg = str_replace( [ "\r", "\n" ], "", $msg );

		if ( !$msg )
		{
			return -3512;
		}

		if ( mb_strlen( $msg, "UTF8" ) > 50 )
		{
			return -3513;
		}

		return 0;
	}

	private function _checkMessageIsLawful( $msg, $uid )
	{
		$textService = new TextService();
		$textService->setCaller( 'class:' . __CLASS__ . ';line:' . __LINE__ );
		$textService->addText( $msg, $uid );

		if ( !$textService->checkStatus() )
		{
			write_log( "notice|聊天包含敏感内容;msg:{$msg};roomid:{$this->_luid};uid:{$uid}", 'room_filter_msg' );

			return -3530;
		}

		return 0;
	}

	/**
	 * 插入消息列表
	 *
	 * @param int    $uid
	 * @param array  $param
	 * @param string $useraddr
	 * @param string $serveraddr
	 *
	 * @return int
	 */
	private function _intoMsgListTable( int $uid, array $param, string $useraddr, string $serveraddr )
	{
		$msgAll = json_encode( [ $param, $useraddr, $serveraddr ] );
		$msgAll = $this->_db->realEscapeString( $msgAll );

		$data = [
			'luid' => $this->_luid,
			'uid'  => $uid,
			'msg'  => $msgAll
		];
		$sql  = $this->_db->insert( self::TABLE_LIVE_USER_MESSAGE, $data, true );

		if ( !$this->_db->query( $sql ) )
		{
			$t = "QueryError @ LiveRoom::userMsg($uid, $useraddr, $serveraddr)[{$this->_db->errno()}][{$this->_db->errstr()}][$sql]";
			$this->_log( $t );

			return 0;
		}

		return $this->_db->insertID;
	}

	/**
	 * 用户送礼
	 *
	 * @param int   $uid    用户ID
	 * @param array $params 附带数据
	 *
	 * @return bool
	 */
	public function sendGift( int $uid, array $params )
	{
		if ( $uid >= LIVEROOM_ANONYMOUS )
		{
			roomerror( -3008 );
		}

		$userObject = new User( $uid, $this->_db );

		$errno  = 0;
		$giftID = intval( $params['gid'] );

		$giftInfo   = Gift::getGiftInfo( $giftID, $this->_db );
		$isSendBean = $giftInfo['type'] == Gift::SEND_TYPE_COIN ? false : true;
		$sendNum    = $isSendBean ? intval( $params['num'] ) : 1;
		$type       = $isSendBean ? Gift::SEND_TYPE_BEAN : Gift::SEND_TYPE_COIN;

		$encpass    = $params['enc'];
		$sendLiveID = $params['liveid'];

		//设置背包送礼基本参数
		$isSendPackGift = 0;
		$packBack       = [];

		if ( !$isSendBean )
		{
			$isSendPackGift = $params['sendType'] ?? 0;
			$isSendPackGift = intval( $isSendPackGift );
			$packidList     = [];
//			$packidList         = isset( $params['packidList'] ) ? params['packid'] : [];
//			$packidList         = explode( ",", $packidList );
//			$packidList         = array_filter( $packidList, function ( $val )
//			{
//				return intval( $val );
//			} );
			//|| $sendNum != count( $packidList ) empty( $packid ) ||
			if ( $isSendPackGift && ( !in_array( $isSendPackGift, Gift::GIFT_SEND_TYPES ) ) )
			{
				$errno = -3520;
			}
		}


		//注意 送礼的时候，需要带上送礼数量
		if ( !$encpass || !$giftID || ( $isSendBean && !in_array( $sendNum, self::SEND_BEAN_ALLOW_LIST ) ) )
		{
			$errno = -3520;
		}
		else
		{
			$liveID   = $this->_anchorObj->getLastLiveId();
			$giftName = $giftInfo['giftname'];

			$this->_log( $giftName );

			//todo liveid 判定 可能去除
			if ( $liveID != $sendLiveID || !$giftInfo )
			{
				$errno = -3511;
			}
			else
			{
				if ( true !== $code = $userObject->checkStateError( $encpass ) )
				{
					$errno = $code;
				}
			}

			if ( !$errno )
			{
				$userInfo   = $this->_getUser( $uid );
				$anchorInfo = $this->_getUser( $this->_luid );
				if ( !$userInfo )
				{
					$errno = -3504;
				}

				if ( !$anchorInfo )
				{
					$errno = -3507;
				}
			}
		}

		if ( !$errno )
		{
			$otid = 0;

			$isTest = in_array( $uid, explode( ',', IOS_TEST_USER_LIST ) );

			if ( $isTest )
			{
				$this->testSendGift( $uid, $userInfo, $anchorInfo, $params, $isSendBean, $type, $giftInfo, $sendNum, $liveID, $errno, $userObject );

				return true;
			}

		}

		//check send pack gift is valid
		if ( !$errno )
		{
			if ( $isSendPackGift )
			{
				$sendPackResult = [];

				if ( !$this->_sendPackGift( $packidList, $uid, $giftID, $sendNum, $sendPackResult ) )
				{
					//neet feed back error info
					$errno = $sendPackResult['errno'];
				}
				else
				{
					//set packBack notify
					$packBack           = $sendPackResult;
					$packBack['giftID'] = $giftID;
				}
			}
		}


		if ( !$errno )
		{
			$sendGiftType = $isSendPackGift;
			$tmpPackId    = isset( $packidList[0] ) ? intval( $packidList[0] ) : 0;
			//TODO  gift record 暂不支持批量送礼，需要以后规划
			if ( !Gift::addGiftRecord( $otid, $uid, $this->_luid, $liveID, $giftID, $sendNum, $type, $this->_db, $sendGiftType, $tmpPackId ) )
			{
				$errno = -3510;
				if ( $isSendPackGift )
				{
					$errlog             = $params;
					$errlog['err_no']   = $errno;
					$errlog['err_desc'] = "add_record_failed";
					$errlog['uid']      = $uid;
					$errlog['luid']     = $this->_luid;
					$this->_sendGiftLog( json_encode( $errno ) );
				}
			}
			else
			{
				// TODO sync to task
			}
		}

		if ( !$errno )
		{
			$desc = [
				'giftid'     => $giftID,
				'giftName'   => $giftName,
				'sendNum'    => $sendNum,
				'giftMoney'  => $isSendBean ? $sendNum : $giftInfo['money'],
				'sendType'   => $isSendPackGift,
				'packidList' => $packidList
			];

			$desc    = json_encode( $desc );
			$finance = new Finance( $this->_db, $this->_redis );

			if ( $isSendBean )
			{
				$cost   = $sendNum;
				$result = $finance->sendBean( $uid, $this->_luid, $cost, $desc, $otid );
			}
			else
			{
				$cost = $sendNum * $giftInfo['money'];
				if ( $isSendPackGift )
				{
					$result = $finance->sendPackGift( $uid, $this->_luid, $cost, $desc, intval( $otid ) );
				}
				else
				{
					$result = $finance->sendGift( $uid, $this->_luid, $cost, $desc, (int)$otid );
				}
			}

			if ( !Finance::checkBizResult( $result ) )
			{
				$this->_log( json_encode( $result ) );
				$errno = $result['errno'];

				if ( $isSendPackGift )
				{
					//todo log to record
					$errlog             = $params;
					$errlog['err_no']   = $errno;
					$errlog['err_desc'] = "finanace_error";
					$errlog['uid']      = $uid;
					$errlog['luid']     = $this->_luid;
					$this->_sendGiftLog( json_encode( $errno ) );
				}
			}
		}

		if ( !$errno )
		{
			$isPhone     = isset( $params['way'] ) ? intval( $params['way'] ) : 0;
			$senderGroup = $this->getUserGroup( $uid );
			$senderNick  = $userInfo['nick'];
			$senderLevel = $userInfo['level'];
			$anchorNick  = $anchorInfo['nick'];

			if ( $isSendBean )
			{
				if ( !Gift::sendGiftSuccessCallBack( $otid, $type, $result['tid'], $result['ctime'], $result['shdd'], $result['rgdd'], $uid, $this->_db ) )
				{

					$errlog             = $params;
					$errlog['err_no']   = 0;
					$errlog['err_desc'] = "send_gift_callback_failed_notice";
					$errlog['fresult']  = $result;
					$errlog['uid']      = $uid;
					$errlog['luid']     = $this->_luid;
					$this->_sendGiftLog( json_encode( $errno ) );
				}
			}
			else
			{
				if ( !Gift::sendGiftSuccessCallBack( $otid, $type, $result['tid'], $result['ctime'], $result['shbd'], $result['rgbd'], $uid, $this->_db ) )
				{
					$errlog             = $params;
					$errlog['err_no']   = 0;
					$errlog['err_desc'] = "send_gift_callback_failed_notice";
					$errlog['fresult']  = $result;
					$errlog['uid']      = $uid;
					$errlog['luid']     = $this->_luid;
					$this->_sendGiftLog( json_encode( $errno ) );
				}
			}

			$exp = Gift::getSendExp( $type, $sendNum, $giftInfo['money'], $giftInfo['exp'] );

			$this->_addSendGiftTimer( $liveID, $uid, $giftID );
			$this->_anchorObj->addAnchorExp( $exp );
			$userObject->addUserExp( $exp );

			$sendTimer = $this->getSendGiftTimer( $liveID, $uid, $giftID );

			if ( $isSendBean )
			{
				$backResult = $userObject->updateUserHpBean( $result['shd'] );
				if ( !$backResult )
				{
					$errlog             = $params;
					$errlog['err_no']   = 0;
					$errlog['err_desc'] = "update_user_coin_failed";
					$errlog['uid']      = $uid;
					$errlog['luid']     = $this->_luid;
					$this->_sendGiftLog( json_encode( $errno ) );
				}

				$backResult = $this->_anchorObj->updateAnchorBean( $result['rgd'] );
				if ( !$backResult )
				{
					$errlog             = $params;
					$errlog['err_no']   = 0;
					$errlog['err_desc'] = "update_user_coin_failed";
					$errlog['uid']      = $uid;
					$errlog['luid']     = $this->_luid;
					$this->_sendGiftLog( json_encode( $errno ) );
				}

				Gift::askRedisIfSendGiftSuccess( $uid, $this->_luid, $type, $result['shdd'], $result['rgdd'] );
				$sendGiftMsgPackage = MsgPackage::getSendBeanMsgSocketPackage( $this->_luid, $uid, $senderNick, $giftID, $giftName, $sendNum, $senderLevel, $isPhone, $senderGroup, $result['rgd'] );
			}
			else
			{
				$backResult = $userObject->updateUserHpCoin( $result['shb'] );
				if ( !$backResult )
				{
					$errlog             = $params;
					$errlog['err_no']   = 0;
					$errlog['err_desc'] = "update_user_coin_failed";
					$errlog['uid']      = $uid;
					$errlog['luid']     = $this->_luid;
					$this->_sendGiftLog( json_encode( $errno ) );
				}


				$backResult = $this->_anchorObj->updateAnchorCoin( $result['rgb'] );
				if ( !$backResult )
				{
					$errlog             = $params;
					$errlog['err_no']   = 0;
					$errlog['err_desc'] = "update_user_coin_failed";
					$errlog['uid']      = $uid;
					$errlog['luid']     = $this->_luid;
					$this->_sendGiftLog( json_encode( $errno ) );
				}

				Gift::askRedisIfSendGiftSuccess( $uid, $this->_luid, $type, $result['shbd'], $result['rgbd'] );
				$backResult = $this->_rankObj->intoRankList( $uid, $cost, $senderNick, $senderLevel );
				if ( !$backResult )
				{
					$errlog             = $params;
					$errlog['err_no']   = 0;
					$errlog['err_desc'] = "update_user_coin_failed";
					$errlog['uid']      = $uid;
					$errlog['luid']     = $this->_luid;
					$this->_sendGiftLog( json_encode( $errno ) );
				}

				//全站礼物通知
//				if ( in_array( $giftID, Gift::SEND_TYPE_GLOBAL_NOTIFY_GIDS ) )
				if ( $giftInfo['all_site_notify'] == Gift::ALL_SITE_NOTIFY_OPEN )
				{
					$treasureID = $this->_createRoomTreasure( $uid );
					if ( !$treasureID )
					{
						$errlog             = $params;
						$errlog['err_no']   = 0;
						$errlog['err_desc'] = "create_treasure_failed";
						$errlog['uid']      = $uid;
						$errlog['luid']     = $this->_luid;
						$this->_sendGiftLog( json_encode( $errno ) );

					}
					else
					{
						$flyGiftMsgPackage = MsgPackage::getSendFlyingGiftMsgSocketPackage( $this->_luid, $uid, $giftID, $giftName, $senderNick, $anchorNick, $treasureID, self::TREASURE_TIME_OUT );
						$this->_sendSocketMsg( $flyGiftMsgPackage );
					}
				}

				$sendGiftMsgPackage = MsgPackage::getSendGiftMsgSocketPackage( $this->_luid, $uid, $senderNick, $giftID, $giftName, $sendNum, $senderLevel, $isPhone, $senderGroup, $sendTimer );
			}

			$this->_sendSocketMsg( $sendGiftMsgPackage );
		}

		$cost = $cost ?? 0;

		$event = new EventManager();
		$event->trigger( $event::ACTION_ANCHOR_DATA_UPDATE, [ 'uid' => $this->_luid ] );
		$event->trigger( $event::ACTION_USER_MONEY_UPDATE, [ 'uid' => $uid ] );


		$property = $userObject->getUserProperty();
		$coin     = $property['coin'];
		$bean     = $property['bean'];
		$mid      = isset( $params['mid'] ) ? intval( $params['mid'] ) : 0;

		if ( $isSendBean )
		{
			$sendCallBackMsgPackage = MsgPackage::getSendBeanMsgCallBackSocketPackage( $this->_luid, $uid, $errno, $mid, $coin, $bean, $cost );
		}
		else
		{
			$sendCallBackMsgPackage = MsgPackage::getSendGiftMsgCallBackSocketPackage( $this->_luid, $uid, $errno, $mid, $coin, $bean, $cost, $isSendPackGift, $packBack );
		}

		return $this->_sendSocketMsg( $sendCallBackMsgPackage );
	}

	private function _sendPackGift( &$packidList, $uid, $giftid, $num, &$sendPackResult )
	{
		$backService = new BackpackService();

		$result = $backService->UseBackpackGiftByGoodsId( $uid, $giftid, $num );

		if ( is_array( $result ) )
		{
			$packidList                = $result['sendGiftId'];
			$sendPackResult['surplus'] = $result['goodsNum'];
//			$sendPackResult['expire'] = $result['validTime'];
			$sendPackResult['giftID'] = $giftid;

			return true;
		}
		else
		{
			$error                   = $backService->getErrorCode();
			$sendPackResult['errno'] = $error;

			return false;
		}
	}

	public function testSendGift( $uid, $userInfo, $anchorInfo, $params, $isSendBean, $type, $giftInfo, $sendNum, $liveID, $errno, User $userObject )
	{

		$giftName = $giftInfo['giftname'];
		$giftID   = intval( $params['gid'] );
		$result   = $userObject->getUserProperty();

		if ( !$errno )
		{

			if ( $isSendBean )
			{
				$cost = $sendNum;
				if ( $result['bean'] < $cost )
				{
					$errno = -3514;
				}
				else
				{
					$result['bean'] -= $cost;
				}
			}
			else
			{
				$cost = $sendNum * $giftInfo['money'];
				if ( $result['coin'] < $cost )
				{
					$errno = -3514;
				}
				else
				{
					$result['coin'] -= $cost;
				}
			}
		}

		if ( !$errno )
		{
			$isPhone     = isset( $params['way'] ) ? intval( $params['way'] ) : 0;
			$senderGroup = $this->getUserGroup( $uid );
			$senderNick  = $userInfo['nick'];
			$senderLevel = $userInfo['level'];
			$anchorNick  = $anchorInfo['nick'];


			$exp = Gift::getSendExp( $type, $sendNum, $giftInfo['money'], $giftInfo['exp'] );

			$this->_addSendGiftTimer( $liveID, $uid, $giftID );
//			$this->_anchorObj->addAnchorExp( $exp );
//			$userObject->addUserExp( $exp );

			$sendTimer = $this->getSendGiftTimer( $liveID, $uid, $giftID );

			if ( $isSendBean )
			{
				$userObject->updateUserHpBean( $result['bean'] );
			}
			else
			{
				$userObject->updateUserHpCoin( $result['coin'] );

				//全站礼物通知
				if ( in_array( $giftID, Gift::SEND_TYPE_GLOBAL_NOTIFY_GIDS ) )
				{
//					$treasureID        = $this->_createRoomTreasure( $uid );
					$treasureID        = time();
					$flyGiftMsgPackage = MsgPackage::getSendFlyingGiftMsgSocketPackageTest( $this->_luid, $uid, $giftID, $giftName, $senderNick, $anchorNick, $treasureID, self::TREASURE_TIME_OUT );
					$this->_sendSocketMsg( $flyGiftMsgPackage );
				}
			}

			$sendGiftMsgPackage = MsgPackage::getSendGiftMsgSocketPackageTest( $this->_luid, $uid, $senderNick, $giftID, $giftName, $sendNum, $senderLevel, $isPhone, $senderGroup, $sendTimer );
			$this->_sendSocketMsg( $sendGiftMsgPackage );
		}

		$cost = $cost ?? 0;

		$property = $userObject->getUserProperty();
		$coin     = $property['coin'];
		$bean     = $property['bean'];
		$mid      = isset( $params['mid'] ) ? intval( $params['mid'] ) : 0;
		if ( $isSendBean )
		{
			$sendCallBackMsgPackage = MsgPackage::getSendBeanMsgCallBackSocketPackage( $this->_luid, $uid, $errno, $mid, $coin, $bean, $cost );
		}
		else
		{
			$sendCallBackMsgPackage = MsgPackage::getSendGiftMsgCallBackSocketPackage( $this->_luid, $uid, $errno, $mid, $coin, $bean, $cost );
		}

		return $this->_sendSocketMsg( $sendCallBackMsgPackage );
	}

	/**
	 * 用户 分享房间发送socket消息
	 *
	 * @param $uid
	 *
	 * @return bool
	 */
	public function shareRoomMsg( $uid )
	{
		if ( !$uid || $uid >= LIVEROOM_ANONYMOUS )
		{
			return false;
		}

		$info  = $this->_getUser( $uid );
		$group = $this->getUserGroup( $uid );

		if ( !$info )
		{
			return false;
		}

		$msgPackage = MsgPackage::getShareRoomMsgSocketPackage( $this->_luid, $uid, $info['nick'], $info['level'], $group );

		return $this->_sendSocketMsg( $msgPackage );
	}

	/**
	 * 用户关注主播socket消息
	 *
	 * @param $uid
	 *
	 * @return bool
	 */
	public function followMsg( $uid )
	{
		if ( !$uid || $uid >= LIVEROOM_ANONYMOUS )
		{
			return false;
		}

		$info  = $this->_getUser( $uid );
		$group = $this->getUserGroup( $uid );

		if ( !$info )
		{
			return false;
		}

		$msgPackage = MsgPackage::getFollowUserMsgSocketPackage( $this->_luid, $uid, $info['nick'], $info['level'], $group );

		return $this->_sendSocketMsg( $msgPackage );
	}

	public function getSendGiftTimer( $liveID, $uid, $giftID )
	{
		$redisKey = $this->_getRedisField( self::REDIS_KEY_LIVE_GIFT_TIMER, $liveID );
		$field    = $this->_getRedisField( $uid, $giftID );

		return (int)$this->_redis->hget( $redisKey, $field );
	}

	private function _addSendGiftTimer( $liveID, $uid, $giftID )
	{
		$timer = $this->getSendGiftTimer( $liveID, $uid, $giftID );
		$timer++;
		$this->_setSendGiftTimer( $liveID, $uid, $giftID, $timer );
	}

	private function _setSendGiftTimer( $liveID, $uid, $giftID, $timer )
	{
		$redisKey = $this->_getRedisField( self::REDIS_KEY_LIVE_GIFT_TIMER, $liveID );
		$field    = $this->_getRedisField( $uid, $giftID );

		$this->_redis->sadd( self::REDIS_KEY_LIVE_GIFT_TIMER_KEY_LIST, $liveID );
		$this->_redis->hset( $redisKey, $field, $timer );
	}

	private function _delSendGiftTimer( $liveID )
	{
		$redisKey = $this->_getRedisField( self::REDIS_KEY_LIVE_GIFT_TIMER, $liveID );

		$this->_redis->del( $redisKey );
		$this->_redis->sRem( self::REDIS_KEY_LIVE_GIFT_TIMER_KEY_LIST, $liveID );
	}

	/**
	 * 直播开始
	 *
	 * @param int $liveid 直播ID
	 *
	 * @return bool
	 */
	public function start( $liveid, $liveType = 0 )
	{
		//TODO:start

		$this->_log( "func:" . __FUNCTION__ . "line:" . __LINE__ . "liveid" . $liveid );
		$msgPackage = MsgPackage::getLiveStartMsgSocketPackage( $this->_luid, $liveid, $liveType );
		$this->_sendSocketMsg( $msgPackage );
		$this->_log( "line:" . __LINE__ . "init statistic start" );

		$this->_startLiveStatistic( $liveid );

		$this->_log( __LINE__ . "  new start" );
		$pushMsg = new LivePush( $this->_db, $this->_redis );
		$this->_log( __LINE__ . " new  end" );
		$userInfo = $this->_anchorObj->getUserInfo();
		$this->_log( __LINE__ . json_encode( $userInfo ) );
		$r = $pushMsg->add( $liveid, $this->_luid, $userInfo );
		$this->_log( __LINE__ . "error:" . json_encode( $r ) );

		return true;
	}

	/**
	 * 直播结束
	 *
	 * @param int    $liveid
	 * @param int    $reasonid
	 * @param string $reason
	 *
	 * @return bool
	 */
	public function stop( $liveid, $reasonid = 0, $reason = '', $liveType = 0 )
	{
		//TODO: stop
		$msgPackage = MsgPackage::getLiveEndMsgSocketPackage( $this->_luid, $liveid, $reasonid, $reason, $liveType );
		$this->_sendSocketMsg( $msgPackage );

//		$this->_endLiveStatistic( $liveid );
		$this->_delSendGiftTimer( $liveid );
		//设置统计信息
		$this->setRoomStatisticPopular( $liveid );
		//清除峰值人数
		$this->setLiveRoomUserCountPeakValue( 0 );

		return true;
	}

	/**
	 * 创建房间宝箱
	 *
	 * @param int $uid 送礼人ID
	 *
	 * @return int 成功返回宝箱ID
	 */
	private function _createRoomTreasure( int $uid )
	{
		$this->_log( __FUNCTION__ );
		$treasureObj = new TreasureBox( $this->_luid, $this->_db, $this->_redis );
		$treasureID  = $treasureObj->createTreasure( $uid );
		$this->_log( "$treasureID" );

		return $treasureID;
	}

	public function getUnReceiveTreasureBoxInfoList( $uid )
	{

		$boxObj = new TreasureBox( $this->_luid, $this->_db, $this->_redis );

		$result = $boxObj->getUnPickedTreasureInfo( $uid );

		$list = $result['list'];

		return array(
			'list'  => $list,
			'count' => $result['total']
		);
	}

	/**
	 * @param $uid
	 * @param $treasureID
	 * @param $result 返回结果，成功返回领豆数量，失败返回错误代码
	 *
	 * @return bool
	 */
	public function openTreasureBox( $uid, $treasureID, &$result )
	{
		$boxObj = new TreasureBox( $this->_luid, $this->_db, $this->_redis );
		$ret    = $boxObj->openTreasure( $uid, $treasureID, $result );
		$this->_log( $result );

		return $ret;
	}

	/**
	 *
	 *
	 * @param $treasureID
	 *
	 * @return bool|int
	 */
	public function getTreasureOwnerUid( $treasureID )
	{
		$boxObj = new TreasureBox( $this->_luid, $this->_db, $this->_redis );
		$ret    = $boxObj->getTreasureOwner( $treasureID );

		return $ret;
	}

	/**
	 * 获取用户在直播间内分组
	 *
	 * @param $uid
	 *
	 * @return int 用户分组数值 1:普通用户，4:房间管理员，5：主播
	 */
	public function getUserGroup( $uid )
	{
		if ( $uid == $this->_luid )
		{
			return 5;
		}

		if ( $this->_anchorObj->isRoomManager( $uid ) )
		{
			return 4;
		}
		else
		{
			return 1;
		}
	}

	public function isShowWel( $level, $uid = 0 )
	{

		$isShowWel = 0;

		if ( $level >= self::SHOW_WEL_LEVEL_BASE_LINE )
		{
			$isShowWel = 1;
		}
		else
		{
			$isShowWel = 0;
		}

		if ( $uid && $isShowWel )
		{

			$robotService = new RoomRobotService();

			$result = $robotService->enterRoomTimer($this->_luid, $uid );

			if(!$result)
			{
				$isShowWel = 0;
			}
		}

		return $isShowWel;
	}


	/**
	 * 获取房间在线用户UIDlist
	 *
	 * @return array array(123,123,123);
	 */
	public function getRoomUser()
	{
		return $this->getRoomUserByLuid( $this->_luid );
	}

	public function getRoomUserByLuid( $luid )
	{
		$result = $this->$this->getRoomUserByLuidFromRedis( $luid );
		if ( empty( $result ) )
		{
			$result = $this->getRoomUserByLuidFromDB( $luid );

			if ( !empty( $result ) )
			{
				//同步redis在线列表
				foreach ( $result as $uid )
				{
					$this->_userEnterRecordByRedis( $uid );
				}
			}
		}

		return $result;
	}

	public function getRoomUserByLuidFromRedis( $luid )
	{
		$result = [];

		for ( $i = 1; $i <= self::ROOM_USER_FIELD_LENGTH; $i++ )
		{
			$field = $this->_getRedisField( $luid, $i );
			$key   = $this->_redis->hget( self::REDIS_KEY_ROOM_USER_HASH, $field );

			if ( $key )
			{
				$list   = $this->_redis->smembers( $key );
				$result = array_merge( $result, $list );
			}
		}

		return $result;
	}

	public function getRoomUserByLuidFromDB( $luid )
	{
		$uidList = array();

		$sql = "select uid from " . self::TABLE_LIVE_ROOM . " where luid={$luid} group by uid";
		$res = $this->_db->query( $sql );

		while ( $row = $res->fetch_assoc() )
		{
			array_push( $uidList, $row['uid'] );
		}

		return $uidList;
	}

	/**
	 * 获取主播列表中各自用户ID
	 *
	 * @param array $luidList
	 *
	 * @return array array(123 =>array(123,123,123));
	 */
	public function getRoomUserByLuidList( array $luidList )
	{
		$result = [];

		foreach ( $luidList as $luid )
		{
			$result[$luid] = $this->getRoomUserByLuid( $luid );
		}

		return $result;
	}


	/**
	 * 为测试账号预留的发送房间消息
	 *
	 * @param array $content
	 *
	 * @return bool
	 */
	public function sendTestRoomMsg( array $content )
	{
		//TODO:sendTestRoomMsg
		return true;
	}

	private function _sendSocketMsg( $msgPackage )
	{
		return SocketSend::sendMsg( $msgPackage, $this->_db );
	}

	/**
	 * 获取当前房间人数
	 *
	 * @return bool|int
	 */
	public function getLiveRoomUserCount()
	{
		return $this->getLiveRoomUserCountByLuid( $this->_luid );
	}

	/**
	 * 根据主播ID获取房间人数
	 *
	 * @param $luid
	 *
	 * @return bool|int
	 */
	public function getLiveRoomUserCountByLuid( int $luid )
	{
		$count = $this->getLiveRoomUserCountByLuidFromRedis( $luid );

		if ( $count == 0 )
		{
			$count = $this->getLiveRoomUserCountByLuidFromDB( $luid );

			if ( $count != 0 )
			{
				//同步redis 在线用户列表
				foreach ( $this->getRoomUserByLuidFromDB( $luid ) as $uid )
				{
					$this->_userEnterRecordByRedis( $uid );
				}
			}
		}

		return $count;
	}

	/**
	 * 根据主播ID通过Redis计算当前房间用户数量
	 *
	 * @param int $luid
	 *
	 * @return int
	 */
	public function getLiveRoomUserCountByLuidFromRedis( int $luid )
	{
		$count = 0;
		for ( $i = 1; $i <= self::ROOM_USER_FIELD_LENGTH; $i++ )
		{
			$field = $this->_getRedisField( $luid, $i );
			$key   = $this->_redis->hget( self::REDIS_KEY_ROOM_USER_HASH, $field );
			if ( $key )
			{
				$count += $this->_redis->scard( $key );
			}
		}

		return $count;
	}

	/**
	 * 根据主播ID通过数据库计算当前房间用户数量
	 *
	 * @param $luid
	 *
	 * @return bool
	 */
	public function getLiveRoomUserCountByLuidFromDB( int $luid )
	{
//		return 100;
		$sql = "select count(DISTINCT(uid)) from " . self::TABLE_LIVE_ROOM . " where luid=$luid";
		$res = $this->_db->query( $sql );

		if ( !$res )
		{
			//TODO LOG
			return false;
		}

		$row = $res->fetch_row();

		return $row[0];
	}

	/**
	 * 获取用戶虚拟人数
	 *
	 * @return int
	 */
	public function getLiveRoomUserCountFictitious()
	{
		//TODO
		$field     = self::REDIS_HFIELD_LIVE_USER_COUNT_FICTITIOUS;
		$viewCount = $this->_getLiveRoomUserCountByRedis( $field, $this->_luid );
		if ( $viewCount < 0 )
		{
			$viewCount = $this->getLiveRoomUserCount();
			$this->_setLiveRoomUserCountByRedis( $field, $viewCount, $this->_luid );
		}

		return $viewCount;
	}

	public function getLiveRoomUserCountFictitiousByLuid( $luid )
	{
		$field     = self::REDIS_HFIELD_LIVE_USER_COUNT_FICTITIOUS;
		$viewCount = $this->_getLiveRoomUserCountByRedis( $field, $luid );

		if ( $viewCount < 0 )
		{
			$viewCount = $this->getLiveRoomUserCountByLuid( $luid );
			$this->_setLiveRoomUserCountByRedis( $field, $viewCount, $luid );
		}

		return $viewCount;
	}

	public function setLiveRoomUserCountFictitious( $count )
	{
		$this->_setLiveRoomUserCountFictitious( $count );
	}

	/**
	 * 设置用户虚拟人数
	 *
	 * @param $count
	 */
	private function _setLiveRoomUserCountFictitious( $count )
	{
		$filed = self::REDIS_HFIELD_LIVE_USER_COUNT_FICTITIOUS;
		$count = (int)$count;

		$this->_setLiveRoomUserCountByRedis( $filed, $count, $this->_luid );
	}

	/**
	 * 计算虚拟人数
	 *
	 * @param $count
	 * @param $add
	 * @param $conf
	 *
	 * @return int
	 */
	private function _getFictitiousViewCount( $count, $add, $conf = array() )
	{
		return $count + $add;
	}

	public function addFictitiousViewCount( $add = 1 )
	{
		$this->_addFictitiousViewCount( $add );
	}

	/**
	 *
	 *
	 * @param $add
	 */
	private function _addFictitiousViewCount( $add = 1 )
	{
		$add   = abs( $add );
		$count = $this->getLiveRoomUserCountFictitious();

		$viewCount = $count + $add;

		$this->_setLiveRoomUserCountFictitious( $viewCount );

	}


	public function subFictitiousViewCount( $sub = 1 )
	{
		$this->_subFictitiousViewCount( $sub );
	}

	private function _subFictitiousViewCount( $sub = 1 )
	{
		$sub   = -abs( $sub );
		$count = $this->getLiveRoomUserCountFictitious();

		$viewCount = $count + $sub;

		$this->_setLiveRoomUserCountFictitious( $viewCount );
	}

	/**
	 * 获取直播间峰值人数
	 *
	 * @return int
	 */
	public function getLiveRomUserCountPeakValue()
	{
		$field     = self::REDIS_HFIELD_LIVE_USER_COUNT_PEAK;
		$viewCount = $this->_getLiveRoomUserCountByRedis( $field, $this->_luid );

		return $viewCount;
	}

	/**
	 * 设置直播间峰值人数
	 *
	 * @param int|null $count
	 *
	 * @return bool
	 */
	private function setLiveRoomUserCountPeakValue( int $count = null )
	{
		$this->_setLiveRoomUserCountPeakValueFictitious( $count );

		$field = self::REDIS_HFIELD_LIVE_USER_COUNT_PEAK;
		if ( $count === null )
		{
			$count    = $this->_getLiveRoomUserCountByRedis( self::REDIS_HFIELD_LIVE_USER_COUNT, $this->_luid );
			$tmpCount = $this->getLiveRoomUserCount();

			if ( $count < $tmpCount )
			{
				$count = $tmpCount;
			}
		}

		$this->_setLiveRoomUserCountByRedis( $field, $count, $this->_luid );

		return true;
	}

	/**
	 * 获取直播间虚拟峰值人数
	 *
	 * @return int
	 */
	public function getLiveRoomUserCountPeakValueFictitious()
	{
		$field     = self::REDIS_HFIELD_LIVE_USER_COUNT_PEAK_FICTITIOUS;
		$viewCount = $this->_getLiveRoomUserCountByRedis( $field, $this->_luid );

		return $viewCount;
	}

	/**
	 * 设置直播间峰值虚拟人数
	 *
	 * @param int|null $count
	 *
	 * @return bool
	 */
	private function _setLiveRoomUserCountPeakValueFictitious( int $count = null )
	{
		$field = self::REDIS_HFIELD_LIVE_USER_COUNT_PEAK_FICTITIOUS;
		if ( $count === null )
		{
			$count    = $this->_getLiveRoomUserCountByRedis( $field, $this->_luid );
			$tmpCount = $this->getLiveRoomUserCountFictitious();

			if ( $count < $tmpCount )
			{
				$count = $tmpCount;
			}
		}

		$this->_setLiveRoomUserCountByRedis( $field, $count, $this->_luid );

		return true;
	}

	/**
	 * redis 这是直播间用户人数
	 *
	 * @param string $field
	 * @param int    $count
	 * @param int    $luid
	 */
	private function _setLiveRoomUserCountByRedis( string $field, int $count, int $luid = 0 )
	{
		$luid      = $luid ? $luid : $this->_luid;
		$tableName = self::REDIS_KEY_LIVE_USER_COUNT;
		$field     = $this->_getRedisField( $field, $luid );

		$setRedisResult = $this->_redis->hset( $tableName, $field, $count );

		$this->_log( __FUNCTION__ . " result is " . json_encode( $setRedisResult ) );
	}

	/**
	 * 获取直播间用户人数
	 *
	 * @param     $field
	 * @param int $luid
	 *
	 * @return int
	 */
	private function _getLiveRoomUserCountByRedis( $field, $luid = 0 )
	{
		$luid      = $luid ? $luid : $this->_luid;
		$tableName = self::REDIS_KEY_LIVE_USER_COUNT;
		$fieldKey  = $this->_getRedisField( $field, $luid );

		$count = $this->_redis->hget( $tableName, $fieldKey );

		return (int)$count;
	}

	/**
	 * 获取一场直播的统计内容
	 *
	 * @param $liveid
	 *
	 * @return array
	 */
	public function getLiveStatisticInfo( $liveid )
	{

		$this->_endLiveStatistic( $liveid );
		$peakValue = $this->_getEndLiveGetPeakValue( $liveid );
		$this->_log( "livestatistic:===>liveID=====>" . $liveid );
		$field  = self::REDIS_HFIELD_LIVE_STATISTIC;
		$result = $this->_getLiveStatisticDataByRedis( $field, $liveid );
		$this->_log( __FUNCTION__ . "===>$result" );
		$result = json_decode( $result, true );

		if ( !$result )
		{
			$this->_log( "livestatistic:===>result is not valid" );
			$coin = 0;
			$bean = 0;
		}
		else
		{
			$coin = (float)$result['end']['coin'] - (float)$result['start']['coin'];
			$bean = (float)$result['end']['bean'] - (float)$result['start']['bean'];
		}

		$this->_log( "livestatistic:===>$liveid:=>>>coin:" . $coin . "=========bean:" . $bean );

		$this->_redis->hdel( self::REDIS_KEY_LIVE_STATISTIC_INFO, $this->_getRedisField( self::REDIS_HFIELD_LIVE_STATISTIC, $liveid ) );

		return [
			'coin' => $coin,
			'bean' => $bean,
			'peak' => $peakValue
		];
	}

	private function _getEndLiveGetPeakValue( $liveid )
	{
		$peakValue = $this->getLiveRoomUserCountPeakValueFictitious();

		if ( $peakValue )
		{
			return $peakValue;
		}

		//todo index anchor_most_popular liveid
		$sql = "select popular from anchor_most_popular where uid={$this->_luid} and liveid=$liveid";
		$res = $this->_db->query( $sql );

		if ( !$res )
		{
			//todo log 
			throw new Exception( "Error Processing Request", 1 );

			return 0;
		}
		else
		{
			$row = $res->fetch_assoc();

			return intval( $row['popular'] );
		}
	}

	/**
	 * 直播开始信息统计
	 *
	 * @param $liveid
	 *
	 * @return bool
	 */
	private function _startLiveStatistic( int $liveid )
	{
		$field = self::REDIS_HFIELD_LIVE_STATISTIC;

		$statistic = $this->_getLiveStatisticDataByRedis( $field, $liveid );

		$this->_log( "livestatistic:===>$liveid:===>start get info" . $statistic );

		//检测当前直播是否已经记录直播开始数据
		//如果记录过，则返回，保证一场直播的完成性
		if ( $statistic && $statistic = json_decode( $statistic, true ) && $statistic['start'] )
		{
			$this->_log( "livestatistic:===>$liveid:==>该直播已经设置" );

			return true;
		}

		$ret = $this->_anchorObj->getAnchorProperty();

		$redisInfo['start'] = $ret;

		return $this->_setLiveStatisticDataByRedis( $field, json_encode( $redisInfo ), $liveid );
	}

	/**
	 * 直播结束信息统计
	 *
	 * @param $liveid
	 */
	private function _endLiveStatistic( $liveid )
	{
		$field = self::REDIS_HFIELD_LIVE_STATISTIC;

		$result = $this->_getLiveStatisticDataByRedis( $field, $liveid );

		$result = json_decode( $result, true );
		if ( is_array( $result ) )
		{
			$result['end'] = $this->_anchorObj->getAnchorProperty();

			if ( !$this->_setLiveStatisticDataByRedis( $field, json_encode( $result ), $liveid ) )
			{
				$this->_log( __FUNCTION__ . "()::key==> LIVE_STATISTIC_INFO field==> $field set redis failed" );
			}
		}
		else
		{
			$this->_log( __FUNCTION__ . "()::key==> LIVE_STATISTIC_INFO field==>" . $field . ":" . $liveid . "reasult is ==>" . json_encode( $result ) );
		}

		//clear redis cache
//		$this->_addToRedisClearCache( RedisCacheManage::REDIS_TYPE_HASH, self::REDIS_KEY_LIVE_STATISTIC_INFO, $field );
	}


	/**
	 * 设置直播统计数据
	 *
	 * @param string $field
	 * @param string $content
	 * @param int    $liveid
	 *
	 */
	private function _setLiveStatisticDataByRedis( string $field, string $content, int $liveid )
	{
		$tableName = self::REDIS_KEY_LIVE_STATISTIC_INFO;
		$field     = $this->_getRedisField( $field, $liveid );

		return $this->_redis->hset( $tableName, $field, $content );
	}

	/**
	 * 获取直播统计数据
	 *
	 * @param string $field
	 * @param int    $liveid
	 *
	 * @return string
	 */
	private function _getLiveStatisticDataByRedis( string $field, int $liveid )
	{
		$tableName = self::REDIS_KEY_LIVE_STATISTIC_INFO;
		$field     = $this->_getRedisField( $field, $liveid );

		return $this->_redis->hget( $tableName, $field );
	}

	private function _getRedisField( $fieldPre, $index )
	{
		return $fieldPre . ":" . $index;
	}

	private function _addToRedisClearCache( $type, $key, $subkey = '' )
	{
		$info = [
			'type'   => $type,
			'key'    => $key,
			'subkey' => $subkey
		];

		RedisCacheManage::addToClearQueue( $info, $this->_redis );
	}

	public function getRoomDayRanking( $size = 10 )
	{
		return $this->_rankObj->getRankList( RoomRank::RANK_TYPE_DAY, $size );
	}

	public function getRoomWeekRanking( $size = 10 )
	{
		return $this->_rankObj->getRankList( RoomRank::RANK_TYPE_WEEK, $size );
	}

	public function getRoomAllRanking( $size = 10 )
	{
		return $this->_rankObj->getRankList( RoomRank::RANK_TYPE_ALL, $size );

	}

	private function _getUser( $uid )
	{
		if ( $uid >= LIVEROOM_ANONYMOUS )
		{
			return [
				'nick'  => '',
				'level' => 1
			];
		}
		if ( $uid == $this->_luid )
		{
			$info          = $this->_anchorObj->getUserInfo( User::USER_INFO_DETAIL );
			$level         = $this->_anchorObj->getAnchorLevel();
			$info['level'] = $level['level'];
		}
		else
		{
			$user = new User( $uid );
			$info = $user->getUserInfo( User::USER_INFO_DETAIL );
		}

		return $info;
	}

	private function _log( $msg )
	{
		$dir = LOG_DIR . __CLASS__ . ".log";
		mylog( $msg, $dir );
	}


	public function _sendGiftLog( $msg )
	{
		$dir = "sendgift_error";
		write_log( $msg, $dir );
	}

	public function log( $msg )
	{
		$this->_log( $msg );
	}

	/**
	 * 获取直播间的观众
	 *
	 * @param type $luid 主播id
	 * @param type $db
	 *
	 * @author yandong
	 * @return array
	 */
	public function getUserPicList( $size = 20 )
	{
		$uids = array();
		$row  = $this->_db->field( 'uid' )->where( "luid= $this->_luid  and  uid != $this->_luid and  uid < " . LIVEROOM_ANONYMOUS . " group by uid" )->order( 'tm DESC' )->limit( $size )->select( 'liveroom' );
		if ( $row )
		{
			foreach ( $row as $v )
			{
				array_push( $uids, $v['uid'] );
			}
		}

		return $uids;
	}

	//todo 以后做成缓存 提高效率以及优化
	public function getUserHeadList( $size )
	{
		$robotList = $this->_redis->smembers( $GLOBALS['env'] . "_robotHeadList" . $this->_luid );
		if ( $robotList )
		{
			$robotList = array_slice( $robotList, 0, $size );
		}
		else
		{
			$robotList = [];
		}

		return $robotList;
	}

	public function setRoomStatisticPopular( $liveid )
	{
		setMostPopual( $this->_luid, $liveid, $this->getLiveRoomUserCountPeakValueFictitious(), $this->_db );
	}
}
