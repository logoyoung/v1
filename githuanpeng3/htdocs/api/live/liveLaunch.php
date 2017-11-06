<?php

/**
 * create a live
 * v 2.0
 * date 2017-09-05
 *
 */

include '../../../include/init.php';

use lib\Anchor;
use lib\live\LiveHelper;
use system\RedisHelper;
use service\live\LiveService;
use service\rule\TextService;
use service\user\UserAuthService;
use lib\live\LiveLog;
use lib\live\Config;

class liveLaunch{

	static $redisname = 'huanpeng';

	static $prefix 	  = 'LIVE_LAUNCH_LOCK_';

	protected $fanscount = 0;
	protected $property = 0;
	protected $params = [
		'uid' => '',
		'encpass' => '',
		'title' => '',
		'gamename' => '',
		'quality'  => 0,
		'orientation' => 0,
		'deviceid' => '',
		'livetype' => 0,
		'longitude' => 0,
		'latitude' => 0
	];
	//protected $publive = [];

	public function getparams($params){
		if(empty($params))
			return false;
		$params = array_change_key_case($params,CASE_LOWER);
		$this->params['quality'] = isset($params['quality'])?trim($params['quality']):0;
		$this->params['orientation'] = isset($params['orientation'])?trim($params['orientation']):0;
		$this->params['livetype'] = isset($params['livetype'])?trim($params['livetype']):0;
		$this->params['longitude'] = isset($params['longitude'])?trim($params['longitude']):0;
		$this->params['latitude'] = isset($params['latitude'])?trim($params['latitude']):0;

		if(empty($params['uid']) || !is_numeric($params['uid']))
			return false;
		$this->params['uid'] = $params['uid'];
		if(empty($params['encpass']) || !is_string($params['encpass']))
			return false;
		$this->params['encpass'] = $params['encpass'];
		if(empty($params['deviceid']))
			return false;
		$this->params['deviceid'] = $params['deviceid'];
		if(empty($params['gamename']) || !is_string($params['gamename']))
			return false;
		$this->params['gamename'] = $params['gamename'];
		if(empty($params['title']) || !is_string($params['title']))
			return false;
		$this->params['title'] = $params['title'];

		if(!empty($params['liveid']) && $params['livetype'] == Config::$live_type['doubelslave'])
			$this->params['liveid'] = $params['liveid'];
		return $this->params = xss_clean($this->params);
	}

	/**
	 * @return int
	 */
	public function filter(){
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
		$textService->addText($this->params['title'], $this->params['uid'], TextService::CHANNEL_THEME)
			->setDeviceId($this->params['deviceid'])
			->setIp($_clientIp);
		$textService->addText($this->params['gamename'], $this->params['uid'], TextService::CHANNEL_THEME)
			->setDeviceId($this->params['deviceid'])
			->setIp($_clientIp);
		//并发获取结果
		$textStatus  = $textService->checkStatus();
		//含敏感内容
		if(array_search(false, $textStatus, true) !== false )
		{
			write_log("notice|主播标题或游戏名称包含敏感内容;title:{$this->params['title']};gamename:{$this->params['gamename']};uid:{$this->params['uid']}",'live_filter_msg');
			//释放锁
			//LiveLaunch::unLockRequest($hash,$redis);
			//这里返回码，有劳guanlong 看看杂改
			//已查看
			//error2( -4109, 2 );
			return Config::$error_code['filter_sensitive'];
		}
		return 0;
	}

	/**
	 * @return int
	 */
	public function authcheck(){
		//login check
		$auth = new UserAuthService();
		$auth->setUid($this->params['uid']);
		$auth->setEnc($this->params['encpass']);
		if($auth->checkLoginStatus() !== true)
			return Config::$error_code['auth_user'];
		// anchor check
		$db = new DBHelperi_huanpeng();
		if(!Anchor::isAnchor($this->params['uid'],$db))
			return Config::$error_code['auth_anchor'];
		return 0;
	}

	public function main(){
		//get params
		$params = $this->getparams($_POST);
		if(!$params)
			error2(Config::$error_code['auth_params']);

		//lock
		if(self::lockrequest($this->params['uid'])){
			LiveLog::applog("the user {$this->params['uid']} can not start live because of lock");
			exit;
		}
		$sensitivecode = $this->filter();
		if($sensitivecode){
			LiveLog::applog("the user {$this->params['uid']} can not start live because of sensetive");
			//unlock
			self::unlockrequest($this->params['uid']);
			error2($sensitivecode);
		}
		//auth check
		$authcode = $this->authcheck();
		if($authcode){
			LiveLog::applog("the user {$this->params['uid']} can not start live because of auth");
			//unlock
			self::unlockrequest($this->params['uid']);
			error2($authcode);
		}
		//get info
		$db = new DBHelperi_huanpeng();
		$Anchor = new Anchor($this->params['uid'], $db);
		$this->fanscount = $Anchor->getFollowNumber();
		$this->property = $Anchor->getAnchorProperty();

		//create live
		$live = $this->createlive();
		if(!isset($live['liveid'])){
			LiveLog::applog("the user {$this->params['uid']} can not start live because of creating failed");
			//unlock
			self::unlockrequest($this->params['uid']);
			error2($live['code']);
		}
		//set cache
		if($this->params['livetype'] != Config::$live_type['doubelslave'])
                {
                    $liveService = new LiveService();
                    $liveService->setCaller('API:' . __FILE__);
                    $liveService->createLiveRedis($live['liveid']);
                }
                
		//unlock
		self::unlockrequest($this->params['uid']);
		$ajaxreturn = [
			'liveID' => $live['liveid'],
			'ctime' => date('Y-m-d H:i:s', time()),
			'notifyServer' => $GLOBALS['env-def'][$GLOBALS['env']]['stream-stop-notify'],
			'liveUploadAddressList' => array($live['server']),
			'stream' => $live['stream'] . "?" .$live['token'],
			'hpbean' => $this->property['bean'],
			'fansCount' => (int) $this->fanscount,
		];
		LiveLog::applog("the user {$this->params['uid']} create live {$live['liveid']}");
		succ($ajaxreturn);
	}


	public function createlive(){
		$master = ($this->params['livetype'] == Config::$live_type['doubelslave']
			&& !empty($this->params['liveid']) )?false:true;
		$live = new LiveHelper($this->params['uid'], $master);
		$params = $this->params;
		unset($params['uid']);
		unset($params['encpass']);
		return $live->createlive($params);
	}

	public static function lockrequest($key, $limit = 1, $expire = 10){
		$redis = self::getinstance();
		$key = self::$prefix . $key;
		try{
			$count = $redis->incr($key);
		}catch (Exception $e){
			//todo log
			return false;
		}
		if ($count > $limit)
			return true;
		else{
			$redis->expire($key, $expire);
			return false;
		}
	}
	public static function unlockrequest($key){
		$redis = self::getinstance();
		$key = self::$prefix . $key;
		try{
			$redis->del($key);
			return true;
		}catch (Exception $e){
			//todo log
			return false;
		}
	}

	public static function getinstance(){
		return RedisHelper::getInstance(self::$redisname);
	}

}

$liveLaunch = new liveLaunch();
$liveLaunch->main();