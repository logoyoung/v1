<?php

/**
 * 发起直播
 */
include '../../../include/init.php';

use lib\Anchor;
use lib\Live;
use service\game\GameService;
use service\rule\TextService;
use service\user\UserAuthService;
use lib\live\LiveLog;
use service\event\EventManager;
use service\live\helper\LiveRedis;
use service\user\UserDataService;
use service\room\RoomManagerService;

class LiveLaunch
{

    public static function lockRequest($key, $redis = null, $limit = 1, $expire = 10)
    {
        if (is_null($redis))
        {
            $redis = new RedisHelp();
        }

        $key = "LOCK_" . $key;

        $redisObj = $redis->getMyRedis();
        $count = $redisObj->incr($key);

        if ($count > $limit)
        {
            return true;
        } else
        {
            $redisObj->expire($key, $expire);

            return false;
        }
    }

    public static function unLockRequest($key, $redis = null)
    {
        if (is_null($redis))
        {
            $redis = new RedisHelp();
        }

        $key = "LOCK_" . $key;

        $redisObj = $redis->getMyRedis();
        $redisObj->del($key);
    }

}

/* * *************************main************************ */
//用户ID
$uid = isset($_POST['uid']) ? trim($_POST['uid']) : '';
//校验码
$encpass = isset($_POST['encpass']) ? trim($_POST['encpass']) : '';
//直播标题
$liveParams['title'] = isset($_POST['title']) ? trim($_POST['title']) : '';
//游戏名称
$liveParams['gamename'] = isset($_POST['gameName']) ? trim($_POST['gameName']) : '';
//直播画质
$liveParams['quality'] = isset($_POST['quality']) ? trim($_POST['quality']) : 0;
//直播角度
$liveParams['orientation'] = isset($_POST['orientation']) ? trim($_POST['orientation']) : 0;
//设备标识
$liveParams['deviceid'] = isset($_POST['deviceID']) ? trim($_POST['deviceID']) : '';
//直播类型
$liveParams['livetype'] = isset($_POST['liveType']) ? trim($_POST['liveType']) : 0;
//主播所在地经度
$liveParams['longitude'] = isset($_POST['longitude']) ? trim($_POST['longitude']) : 0;
//主播所在地纬度
$liveParams['latitude'] = isset($_POST['latitude']) ? trim($_POST['latitude']) : 0;
$liveParams = xss_clean($liveParams);
//必填参数不能为空
if (empty($uid) || empty($encpass) || empty($liveParams['title']) || empty($liveParams['gamename']) || empty($liveParams['deviceid']))
{
    error2(-4013, 2);
}

/*
 * 接入并发过滤 30s限制一个请求
 */
$hash = md5($uid);
$redis = new RedisHelp();
$r = LiveLaunch::lockRequest($hash, $redis);
if ($r)
{
    //mylog("liveLaunch failed request count\n", LOG_DIR . 'liveLaunch.log');
	LiveLog::applog("notice:liveLaunch failed request count");
    exit;
} else
    //mylog("liveLaunch success request count\n", LOG_DIR . 'liveLaunch.log');
	LiveLog::applog("record:liveLaunch success request count");

/*  接入反垃圾服务
 *
 * PS : 1.此服务调用第三方服务，线上超时时间为1s,目前校验服务为最低级，即使反垃圾服务挂了也不会影响直播服务。
 *      2.关于dev pre 响应慢的问题，因dev 、pre服务器为铁通网络，跨服务商调用ping 近100ms,还存在掉包情况，暂时没办法，忍忍吧。
 *      3.出问题怎么查？
 *        a.所有被反垃圾的都会在live_filter_msg有记录
 *        b.所有请求第方服务的日志都会在http_access.log 或 http_error.log 都会有记录
 *
 *      4.测式设备总是被反垃圾服务怎么办？
 *      	反垃圾服务器通过机器学习DeviceId，ip等，可通过管理后台添加白名单解决。
 */
$textService = new TextService();
  $textService->setCaller('api:'.__FILE__.';line:'.__LINE__);
  $port = 0;
  $_clientIp   = fetch_real_ip($port);
  //关闭后如果接请求反垃圾接口网络服务异常都会返回true,默认通过
  //$textService->setCallLevel(true);
  $textService->addText($liveParams['title'], $uid, TextService::CHANNEL_THEME)
  ->setDeviceId($liveParams['deviceid'])
  ->setIp($_clientIp);

  $textService->addText($liveParams['gamename'], $uid, TextService::CHANNEL_THEME)
  ->setDeviceId($liveParams['deviceid'])
  ->setIp($_clientIp);
  //并发获取结果
  $textStatus  = $textService->checkStatus();
  //含敏感内容
  if(array_search(false, $textStatus, true) !== false )
  {
  write_log("notice|主播标题或游戏名称包含敏感内容;title:{$liveParams['title']};gamename:{$liveParams['gamename']};uid:{$uid}",'live_filter_msg');
  //释放锁
  LiveLaunch::unLockRequest($hash,$redis);
  //这里返回码，有劳guanlong 看看杂改
  //已查看
  error2( -4109, 2 );
  }

$db = new DBHelperi_huanpeng();

/*//用户类型
if (!Anchor::isAnchor($uid, $db))
{
    LiveLaunch::unLockRequest($hash, $redis);
    error2(-4057, 2);
}
//登录检测
$Anchor = new Anchor($uid, $db);
$loginErrCode = $Anchor->checkStateError($encpass);
if ($loginErrCode !== true)
{
    LiveLaunch::unLockRequest($hash, $redis);
    error2($loginErrCode, 2);
}*/

//权限校验

$auth = new UserAuthService();
$auth->setUid($uid);
$auth->setEnc($encpass);

if($auth->checkLoginStatus() !== true)
{
	LiveLaunch::unLockRequest($hash,$redis);
	$errorCode = $result['error_code'];
	error2( '-1013', 2 );
	exit;
}
if( !Anchor::isAnchor( $uid, $db ) )
{
	LiveLaunch::unLockRequest($hash,$redis);
	error2( -4057, 2 );
}

$Anchor = new Anchor($uid, $db);
$fansCount = $Anchor->getFollowNumber();
$property = $Anchor->getAnchorProperty();
$conf = $GLOBALS['env-def'][$GLOBALS['env']];

$Live = new Live($uid, $db);

$liveCreateBack = $Live->createLive($liveParams);
if (!isset($liveCreateBack['liveID']))
{
    LiveLaunch::unLockRequest($hash, $redis);
    error2($liveCreateBack, 2);
}

$params = [];
$time = time();
$liveInfo = [];
$userService = new UserDataService();
$userService->setCaller('api:' . __FILE__);
$userService->setUid($uid);
$userInfo = $userService->getUserInfo();

$roomManagerService = new RoomManagerService();
$roomManagerService->setCaller('api:' . __FILE__);
$roomManagerService->setUid($uid);

$gameId = GameService::getGameIdByGameName($liveParams['gamename']);

if(!$gameId)
{
    $gameId = GameService::getGameIdByGameName('其他游戏');
}

$liveInfo['liveid'] = $liveCreateBack['liveID'];
$liveInfo['uid'] = $uid;
$liveInfo['head'] = isset($userInfo['pic']) ? $userInfo['pic'] : '';
$liveInfo['nick'] = isset($userInfo['nick']) ? $userInfo['nick'] : '';
$liveInfo['roomID'] = $roomManagerService->getRoomidByUid();
$liveInfo['poster'] = CROSS;
$liveInfo['ispic']  = '0';
$liveInfo['title'] = $liveParams['title'];
$liveInfo['gameName'] = $liveParams['gamename'];
$liveInfo['gamename'] = $liveParams['gamename'];//兼容不同端取字段大小写不同
$liveInfo['gameid'] = $gameId;
$liveInfo['stime'] = date('Y-m-d H:i:s', $time);
$liveInfo['ctime'] = date('Y-m-d H:i:s', $time);
$liveInfo['orientation'] = $liveParams['orientation'];
$liveInfo['viewCount'] = 0;
$liveInfo['fansCount'] = $fansCount;
$liveInfo['status'] = LIVE;

$params['liveinfo'] = [$liveInfo];

$params['livestatus'][0]['liveid'] = $liveCreateBack['liveID'];
$params['livestatus'][0]['status'] = LIVE;

$params['gamelivecount']['gameid'] = $gameId;

$params['liveid'] = $liveCreateBack['liveID'];
$params['uid']    = $uid;

$types = LiveRedis::$sortType;
foreach($types as $type)
{
    if($type == LiveRedis::LIVE_LIST_BY_VIEW_COUNT)
    {
        $params['livelist'][$type]['score'] = 0;
        $params['gamelivelist'][$type]['score'] = 0;
    } elseif ($type == LiveRedis::LIVE_LIST_BY_CTIME)
    {
        $params['livelist'][$type]['score'] = $time;
        $params['gamelivelist'][$type]['score'] = $time;
    } elseif ($type == LiveRedis::LIVE_LIST_BY_FOLLOW_COUNT)
    {
        $params['livelist'][$type]['score'] = $fansCount;
        $params['gamelivelist'][$type]['score'] = $fansCount;
    }
    $params['livelist'][$type]['uid'] = $uid;
    $params['gamelivelist'][$type]['uid'] = $uid;
    $params['gamelivelist'][$type]['gameid'] = $gameId;
}

$event = new EventManager();

$event->trigger(EventManager::ACTION_LIVE_START, $params);

$event = null;

//mylog("用户{$uid}创建了直播：{$liveCreateBack['liveID']}", LOG_DIR . 'Live.error.log');
LiveLog::applog("record:用户{$uid}创建了直播：{$liveCreateBack['liveID']}");
LiveLaunch::unLockRequest($hash, $redis);
succ(array(
    'liveID' => $liveCreateBack['liveID'],
    'ctime' => date('Y-m-d H:i:s', time()),
    'notifyServer' => $conf['stream-stop-notify'],
    'liveUploadAddressList' => array($liveCreateBack['rtmpServer']),
    'stream' => $liveCreateBack['stream'],
    'hpbean' => $property['bean'],
    'fansCount' => (int) $fansCount
));
exit;






