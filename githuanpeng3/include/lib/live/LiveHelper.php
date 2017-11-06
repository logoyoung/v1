<?php
/**
 * live helper
 * v 1.0
 * date 2017-09-04
 */

namespace lib\live;


use Exception;
use lib\LiveRoom;
use system\DbHelper;
use system\RedisHelper;
use lib\live\Config;
use lib\CDNHelper;
use lib\WcsHelper;
use lib\Anchor;
use lib\SiteMsgBiz;
use lib\MsgPackage;
use service\game\GameService;
use service\event\EventManager;



class LiveHelper{

	const STOP_TYPE_ANCHOR = '0';
	const STOP_TYPE_TIMEOUT = '1';
	const STOP_TYPE_ADMIN = '2';
	const STOP_TYPE_EXCEPTION = '3';
	const STOP_TYPE_MASTER = '4';

	static $stopreason = [
		'0' => '主播停止直播',
		'1' => '您的设备网络连接超时，直播已结束！',
		'2' => '您的直播内容违规，已被管理结束直播！',
		'3' => '直播异常结束',
		'4' => '您已结束双屏直播',
	];

	static $dbname = 'huanpeng';
	static $redisname = 'huanpeng';
	static $master_live_table = "live";
	static $slave_live_table  = 'slavelive';
	static $master_stream_table      = 'liveStreamRecord';
	static $slave_stream_table      = 'slaveStreamRecord';
	static $other_game = '401';
	static $master_stream_prefix = 'Y';
	static $slave_stream_prefix = 'S';
	static $cdn = null;
	//static $liveheart = 'liveheart';

	protected $live_table = '';
	protected $stream_table = '';
	protected $live = [];
	protected $master = null;



	public function __construct($uid = null,$master = true){
		if(!is_numeric($uid))
			return false;
		//init
		$this->master = $master;
		$port = '';
		$ip = fetch_real_ip($port);
		$ip = ip2long($ip);
		$this->live = [
			'ip' => $ip,
			'port' => $port,
			'uid'  => $uid,
			'server' => $GLOBALS['env-def'][$GLOBALS['env']]['stream-pub'],
		];
		$this->live_table = $master?self::$master_live_table:self::$slave_live_table;
		$this->stream_table = $master?self::$master_stream_table:self::$slave_stream_table;
		return true;
	}
	public static function getInstance(){
		return DbHelper::getInstance(self::$dbname);
	}
	public static function getredis(){
		return RedisHelper::getInstance(self::$redisname);
	}
	private function _precreate( $live ){
		//发直播权限检测
		$db = new \DBHelperi_huanpeng();
		if (Anchor::isSendLive($this->live['uid'], $db) !== true)
			return Config::$error_code['live_pub'];
		if(!$this->master){
			$masterlive = $this->getReccentLive($this->live['uid'], true,self::$master_live_table);
			if(empty($masterlive))
				return Config::$error_code['master_stop'];
		}
		$reccentlive = $this->getReccentLive($this->live['uid'], true);
		if(empty($reccentlive))
			return [];
		if($live['deviceid'] != $reccentlive['deviceid'])
			return Config::$error_code['live_device'];
		return $reccentlive;
	}

	public function createlive($live){
		$reccentlive = $this->_precreate($live);
		//auth
		if(!is_array($reccentlive))
			return ['code'=>$reccentlive];
		//continue
		if(is_array($reccentlive)&&count($reccentlive)){
			$this->live['liveid'] = $reccentlive['liveid'];
			$this->live['stream'] = $reccentlive['stream'];
			//set stream
			$streamstatus = $this->getstream($this->live['stream'],['status']);
			if($streamstatus <= STREAM_START )
				$this->setstream($this->live['stream'],['status'=>STREAM_COVER],$streamstatus);
		}
		//create live
		else{
			$game = $this->getgame($live['gamename']);
			$live['gameid'] = $game['gameid'];
			$live['gametid'] = $game['gametid'];
			$live['utime']   = date('Y-m-d H:i:s');
			$this->live = $this->live + $live;

			$id = $this->create($this->live,$this->live_table);
			$liveid = empty($this->live['liveid'])?$id:$this->live['liveid'];
			if( !$liveid )
				return ['code'=>Config::$error_code['live_fail']];
			$this->live['liveid'] = $liveid;
		}
		//create stream
		//get streamname
		$prefix = $this->master?self::$master_stream_prefix:self::$slave_stream_prefix;
		$stream = $prefix . "-" . $this->live['liveid'] . "-" . date("YmdHis");
		$this->live['stream'] = $stream;
		$streaminfo = [
			'liveid' => $this->live['liveid'],
			'server' => $this->live['server'],
			'stream' => $this->live['stream'],
			'utime'  => date("Y-m-d H:i:s"),
			'status' => STREAM_CREATE,
		];

		if(!$this->create($streaminfo,$this->stream_table))
			return ['code'=>Config::$error_code['stream_fail']];
		//update live
		$this->updatelivestream($this->live['liveid'], $this->live['stream']);
		$token = $this->getpubtoken($this->live['liveid'],$this->live['uid']);
		return [
			'liveid' => $this->live['liveid'],
			'server' => $this->live['server'],
			'stream' => $this->live['stream'],
			'token'	 => $token,
		];
	}

	public function startlive($time = null){
		$live = $this->getReccentLive($this->live['uid'],true);
		if(empty($live))
			return false;
		$time = isset($time) ? date("Y-m-d H:i:s",$time) : date("Y-m-d H:i:s");
		//start live
		$datalive = [
			'status' => LIVE,
			'stime' => $time,
			'utime' => date('Y-m-d H:i:s'),
		];
		if(!$this->updatelivebyid($live['liveid'],$datalive))
			return false;
		//start stream
		$datastream = [
			'status' => STREAM_START,
			'utime'  => date('Y-m-d H:i:s'),
			'stime'	 => $time,
		];
		$this->setstream($live['stream'],$datastream, STREAM_CREATE);
		//room msg
		$msg = [
			'liveid' => $live['liveid'],
			'livetype' => $live['livetype'],
			'uid'	 => $this->live['uid'],
		];
		self::sendmsg($msg,'start');
		return true;
	}

	public function disconnect($time = null){
		$live = $this->getReccentLive($this->live['uid'],true);
		if(empty($live))
			return false;
		$time = isset($time) ? date("Y-m-d H:i:s",$time) : date("Y-m-d H:i:s");
		//start stream
		$datastream = [
			'status' => STREAM_DISCONNECT,
			'utime'  => date('Y-m-d H:i:s'),
			'etime'	 => $time,
		];
		return $this->setstream($live['stream'],$datastream, STREAM_CREATE);
	}

	public function anchorstoplive(){
		return $this->stoplive();
	}

	public function adminstoplive(){
		return $this->stoplive(false);
	}

	public function stoplive($type = true){
		$live = $this->getReccentLive($this->live['uid']);
		if(empty($live))
			return false;
		if($this->master)
			self::stopLiveRedis($live);
		if ((int) $live['status'] > LIVE)
			return true;
		//stop live
		$datalive = [
			'status' => LIVE_STOP,
			'etime' => date('Y-m-d H:i:s'),
			'utime' => date('Y-m-d H:i:s'),
			'stop_reason' => $type?self::STOP_TYPE_ANCHOR:self::STOP_TYPE_ADMIN,
		];
		if(!$this->updatelivebyid($live['liveid'],$datalive))
			return false;
		//stop stream
		$datastream = [
			'status' => $type?STREAM_USER_STOP:STREAM_ADMIN_STOP,
			'utime'  => date('Y-m-d H:i:s'),
			'etime'	 => date('Y-m-d H:i:s'),
		];
		$this->setstream($live['stream'],$datastream, STREAM_CREATE);
		if($this->master){
			$datalive['stop_reason'] = self::STOP_TYPE_MASTER;
			//$datastream['status'] = $type?STREAM_USER_STOP:STREAM_ADMIN_STOP;
			$this->updatelivebyid($live['liveid'],$datalive,false);
			$this->setstream($live['stream'],$datastream, STREAM_CREATE,false);
		}
		$msg = [
			'liveid' => $live['liveid'],
			'type'   => $type?self::STOP_TYPE_ANCHOR:self::STOP_TYPE_ADMIN,
			'uid'	 => $this->live['uid'],
			'livetype' => $live['livetype'],
		];
		self::sendmsg($msg,'stop');
		return true;
	}

	public static function timeout($live,$master = true){
		if(empty($live))
			return false;
		//$this->stopLiveRedis($live);
		if($master)
			self::stopLiveRedis($live);
		if ((int) $live['status'] > LIVE)
			return true;
		//check
		$table = $master?self::$master_live_table:self::$slave_live_table;
		//stop live
		$datalive = [
			'status' => LIVE_STOP,
			'etime' => date('Y-m-d H:i:s'),
			'utime' => date('Y-m-d H:i:s'),
			'stop_reason' => self::STOP_TYPE_TIMEOUT,
		];
		if(!self::update(['liveid'=>$live['liveid']],$datalive,$table))
			return false;
		//stop stream
		$datastream = [
			'status' => STREAM_TIMEOUT,
			'utime'  => date('Y-m-d H:i:s'),
			'etime'	 => date('Y-m-d H:i:s'),
		];
		self::staticsetstream($live['stream'],$datastream,STREAM_CREATE,$master);
		if($master){
			$datalive['stop_reason'] = self::STOP_TYPE_MASTER;
			$datastream['status'] = STREAM_USER_STOP;
			self::update(['liveid'=>$live['liveid']],$datalive,self::$slave_live_table);
			self::staticsetstream($live['stream'],$datastream,STREAM_CREATE,false);
		};
		//send msg
		$lastlive = self::getlivebykey(['uid' => $live['uid']],['liveid'],$table,1,"DESC");
		if(isset($lastlive[0]['liveid']) && $lastlive[0]['liveid'] == $live['liveid']){
			$msg = [
				'liveid' => $live['liveid'],
				'type'   => self::STOP_TYPE_TIMEOUT,
				'uid'	 => $live['uid'],
				'livetype' => $live['livetype'],
			];
			self::sendmsg($msg,'stop');
		}
		return true;
	}

	/**
	 *
	 * get user master reccently live
	 * @param string $uid
	 * @param bool   $live
	 *
	 * @return array
	 */
	public  function getReccentLive($uid = '', $living = false, $table = ''){
		if(empty($uid) || !is_numeric($uid))
			return [];
		$params = [
			'uid' => $uid,
		];
		$table = $table?$table:$this->live_table;
		$sql = "SELECT * FROM `{$table}` WHERE `uid`=:uid ORDER BY liveid DESC LIMIT 1";
		$db = self::getInstance();
		try{
			$result = $db->query($sql, $params);
			$result = isset($result[0])?$result[0]:[];
		}catch (Exception $e){
			//return [];
			throw $e;
		}
		if(!$living)
			return $result;
		if(isset($result['status']) && ($result['status'] == Config::LIVE || $result['status'] == Config::LIVE_CREATE ))
			return $result;
		return [];
	}


	public  function setstream($key = '',$field = [],$status = 0,$master = true){
		if( !$key || empty($field) )
			return false;
		$table = $master?$this->stream_table:self::$slave_stream_table;
		return self::savestream($key,$field,$table,$status);

	}
	public static function staticsetstream($key = '',$field = [],$status = 0,$master = true){
		if( !$key || empty($field) )
			return false;
		$table = $master?self::$master_stream_table:self::$slave_stream_table;
		return self::savestream($key,$field,$table,$status);
	}
	public static function savestream($key,$field,$table,$status){
		$sql = "UPDATE " . $table . " SET ";
		$update = [];
		foreach ($field as $k => $v){
			$update[] = "`{$k}`=:{$k}";
		}
		$update = implode(',',$update);
		$sql .= $update . " WHERE `stream`=:streamkey AND `status`>=:statuskey AND `status`<:stop";
		$bdparams = $field;
		$bdparams['streamkey'] = $key;
		$bdparams['statuskey'] = $status;
		$bdparams['stop']	= STREAM_DISCONNECT;

		$db = self::getInstance(self::$dbname);
		try{
			$rowcount = $db->execute($sql,$bdparams);
			if(!$rowcount)
				return false;
			return true;
		}catch (Exception $e){
			throw $e;
		}
	}
	public  function getstream($key = '',$field = []){
		if( !$key  )
			return false;
		if(empty($field))
			$sqlfield = "*";
		else
			$sqlfield = implode(',',$field);
		$db = self::getInstance();
		$sql = "SELECT " . $sqlfield . " FROM " . $this->stream_table . " WHERE `stream`=:stream ";
		$bdparams = [
			'stream' => $key,
		];
		try{
			$result = $db->query($sql, $bdparams);
			return empty($result[0])?[]:$result[0];
		}catch (Exception $e){
			return [];
		}
	}

	public  function create($field, $table){
		if(empty($field)||!is_array($field))
			return false;
		$sqlfield = implode(',',array_keys($field));
		$sqlvalue = array_map(function($v){
			return ":$v";
		},array_keys($field));
		$sqlvalue = implode(",",$sqlvalue);
		$sql = "INSERT INTO " . $table . "(" . $sqlfield . ") VALUES(" . $sqlvalue . ")";
		$db = self::getInstance();
		try{
			$db->execute($sql, $field);
			if($lastid = $db->lastInsertId())
				return $lastid;
			return false;
		}catch (Exception $e){
			throw $e;
		}
	}

	public  function getgame($gamename){
		$sql = "SELECT `gameid`,`gametid` FROM `game` WHERE `name`=:name";
		$db = self::getInstance();
		try{
			$result = $db->query($sql, ['name'=>$gamename]);
			return (empty($result[0]))?['gameid' => self::$other_game, 'gametid' => '']:$result[0];
		}catch (Exception $e){
			return ['gameid' => self::$other_game, 'gametid' => ''];
		}
	}
	public function updatelivestream($liveid,$stream){
		if(!is_numeric($liveid) || !is_string($stream))
			return false;
		$sql = "UPDATE " . $this->live_table . " SET `stream`=:stream WHERE `liveid`=:liveid ORDER BY `ctime` DESC LIMIT 1";
		$db = self::getInstance();
		$affect = $db->execute($sql, ['stream'=>$stream,'liveid'=>$liveid]);
		return $affect?true:false;
	}
	public function updatelivebyid($liveid,$field =[],$master = true){
		if(!$liveid || !is_numeric($liveid) || !is_array($field)||!count($field))
			return false;
		foreach ($field as $k => $v){
			$sqlfield[] = "$k=:$k";
		}
		$sqlfield = implode(",",$sqlfield);
		$field['liveid'] = $liveid;
		$table = $master?$this->live_table:self::$slave_live_table;
		$sql = "UPDATE " . $table . " SET " . $sqlfield . " WHERE `liveid`=:liveid ORDER BY `ctime` DESC LIMIT 1";
		$db = self::getInstance();
		try{
			$affect = $db->execute($sql,$field);
			return $affect?true:false;
		}catch (Exception $e){
			throw $e;
		}

	}
	public function getcnd(){
		if(self::$cdn)
			return self::$cdn;
		return new CDNHelper();
	}
	public function getpubtoken($liveid,$uid){
		$prefix = $this->master?self::$master_stream_prefix:self::$slave_stream_prefix;
		$data = [
			'liveid' => $prefix . "-" . $liveid,
			'uid' => $uid,
			'tm' => time(),
		];
		$data['sign'] = CDNHelper::getPublishLiveSecret($data);
		$token = http_build_query($data);
		return $token;
	}

	/**
	 * 停止直播缓存修改
	 * @param array $liveData
	 * @return boolean
	 */
	public static function stopLiveRedis($liveData = [])
	{

		try
		{
			$gameId = GameService::getGameIdByGameName($liveData['gamename']);

			if (!$gameId)
			{
				$gameId = GameService::getGameIdByGameName('其他游戏');
			}


			$params = [];

			$params['uid'] = $liveData['uid'];
			$params['gameid'] = $gameId;

			$params['livestatus'][0]['liveid'] = $liveData['liveid'];
			$params['livestatus'][0]['status'] = LIVE_STOP;

			$params['gamelivecount']['gameid'] = $gameId;

			$event = new EventManager();

			$event->trigger(EventManager::ACTION_LIVE_STOP, $params);

			$event = null;
			return true;
		} catch (Exception $e)
		{
			return false;
		}
	}

	public static function sendmsg($msg,$type = null){
		if(!$type)
			return false;
		$redis = new \RedisHelp();
		$db = new \DBHelperi_huanpeng();
		$room = new LiveRoom($msg['uid'],$db,$redis);
		if($type == 'stop')
			return $room->stop($msg['liveid'], $msg['type'], self::$stopreason[$msg['type']], $msg['livetype']);
		elseif($type == 'start')
			return $room->start($msg['liveid'],$msg['livetype']);
	}

	public static function getlivebystream($key,$field = [],$master = true){
		if( !$key  )
			return false;
		if(empty($field))
			$sqlfield = "*";
		else
			$sqlfield = implode(',',$field);
		$db = self::getInstance();
		$table = $master?self::$master_live_table:self::$slave_live_table;
		$sql = "SELECT " . $sqlfield . " FROM " . $table . " WHERE `stream`=:stream ";
		$bdparams = [
			'stream' => $key,
		];
		try{
			$result = $db->query($sql, $bdparams);
			return empty($result[0])?[]:$result[0];
		}catch (Exception $e){
			return [];
		}
	}

	public static function checkauthpubstream($liveid,$master = true){
		if(!isset($liveid) || !is_numeric($liveid))
			return false;
		$table = $master?self::$master_stream_table:self::$slave_stream_table;
		$sql = "SELECT `status` FROM " . $table . " WHERE `liveid`=:liveid ORDER BY `id` DESC LIMIT 1";
		$db = self::getInstance();
		$bdparams = ['liveid'=>$liveid];
		try{
			$result = $db->query($sql,$bdparams);
			if(isset($result[0]) && ($result[0]['status'] == STREAM_CREATE))
				return true;
			return false;
		}catch (Exception $e){
			return false;
		}
	}
	public static function update($key,$field,$table){
		if(empty($key) || empty($field) ||empty($table))
			return false;
		foreach ($field as $k => $v){
			$sqlfield[] = "$k=:$k";
		}
		$sqlfield = implode(",",$sqlfield);
		foreach ($key as $k => $v){
			$where[] = "$k=:{$k}key";
			$field["{$k}key"] = $v;
		}
		$where = implode(",",$where);
		$sql = "UPDATE " . $table . " SET " . $sqlfield . " WHERE " . $where;
		$db = self::getInstance();
		try{
			$affect = $db->execute($sql, $field);
			return $affect?true:false;
		}catch (Exception $e){
			throw $e;
		}
	}

	public static function getlivebykey($key,$field,$table,$limit = 0, $order = 'ASC'){
		if(empty($key) || empty($field) ||empty($table))
			return false;
		$sqlfield = implode(",",$field);
		foreach ($key as $k => $v){
			$where[] = "$k=:{$k}key";
			$bdparams["{$k}key"] = $v;
		}
		$where = implode(",",$where);
		$sql = "SELECT " . $sqlfield . " FROM " . $table . " WHERE " . $where;
		if($limit)
			$sql .= " ORDER BY `ctime` " . $order . " LIMIT " . $limit;
		$db = self::getInstance();
		try{
			$result = $db->query($sql,$bdparams);
			return empty($result)?[]:$result;
		}catch (Exception $e){
			throw $e;
		}
	}
	public static function getstreambykey($key,$field,$table,$limit = 0, $order = 'ASC'){
		if(empty($key) || empty($field) ||empty($table))
			return false;
		$sqlfield = implode(",",$field);
		foreach ($key as $k => $v){
			$where[] = "$k=:{$k}key";
			$bdparams["{$k}key"] = $v;
		}
		$where = implode(",",$where);
		$sql = "SELECT " . $sqlfield . " FROM " . $table . " WHERE " . $where;
		if($limit)
			$sql .= " ORDER BY `id`" . $order . " LIMIT " . $limit;
		$db = self::getInstance();
		try{
			$result = $db->query($sql,$bdparams);
			return empty($result)?[]:$result;
		}catch (Exception $e){
			throw $e;
		}
	}

	public static function getplayurl($uid = ''){
		if(empty($uid)||!is_numeric($uid))
			return false;
		$field = [
			'liveid',
			'stream',
			'status',
			'orientation',
			'uid',
			'title',
			'gamename',
			'livetype',
			'poster',
			'gameid',
			'gametid',
		];
		$data = [];
		$masterlive = self::getlivebykey(['uid'=>$uid],$field,self::$master_live_table,1,"DESC");
		if(empty($masterlive[0]))
			return $data;
		$masterlive = $masterlive[0];
		$masterlive['server'] = $GLOBALS['env-def'][$GLOBALS['env']]['stream-watch'];
		$masterlive['token'] = CDNHelper::getPlayLiveSecret($masterlive['stream']);
		$streamstatus = self::getstreambykey(['stream'=>$masterlive['stream']],['status'],self::$master_stream_table);
		$masterlive['streamstatus'] = $streamstatus[0]['status'];
		if($masterlive['status'] == LIVE && $masterlive['streamstatus'] == STREAM_START)
			$playtype = Config::$play_type['play'];
		elseif(($masterlive['status'] == LIVE && $masterlive['streamstatus'] != STREAM_START)||$masterlive['status'] == LIVE_CREATE)
			$playtype = Config::$play_type['disconnect'];
		else
			$playtype = Config::$play_type['stop'];
		$masterlive['playtype'] = $playtype;
		$data['master'] = $masterlive;
		if($masterlive['livetype'] == Config::$live_type['doubelmaster'])
			$slavelive = self::getlivebykey(['liveid'=>$masterlive['liveid']],$field,self::$slave_live_table,1,'DESC');
		if(empty($slavelive[0]))
			return $data;
		$slavelive = $slavelive[0];
		$slavelive['server'] = $GLOBALS['env-def'][$GLOBALS['env']]['stream-watch'];
		$slavelive['token'] = CDNHelper::getPlayLiveSecret($slavelive['stream']);
		$streamstatus = self::getstreambykey(['stream'=>$slavelive['stream']],['status'],self::$slave_stream_table);
		$slavelive['streamstatus'] = $streamstatus[0]['status'];
		if($slavelive['status'] == LIVE && $slavelive['streamstatus'] == STREAM_START)
			$playtype = Config::$play_type['play'];
		elseif(($slavelive['status'] == LIVE && $slavelive['streamstatus'] != STREAM_START)||$slavelive['status'] == LIVE_CREATE)
			$playtype = Config::$play_type['disconnect'];
		else
			$playtype = Config::$play_type['stop'];
		$slavelive['playtype'] = $playtype;
		$data['slave'] = $slavelive;
		return $data;
	}

	public static function getstreamstatus($stream){
		$redis = self::getredis();
		try{
			return $redis->hGet(Config::$live_heart,$stream);
		}catch (Exception $e){
			//todo log
			return false;
		}
	}

	public static  function setstreamstatus($stream, $value){
		$redis = self::getredis();
		try{
			return $redis->hSet(Config::$live_heart, $stream, $value);
		}catch (Exception $e){
			return false;
		}
	}

	public static  function delstreamstatus($stream){
		$redis = self::getredis();
		try{
			return $redis->hDel(Config::$live_heart,$stream);
		}catch (Exception $e){
			return false;
		}
	}

	public static function getallstreamstatus(){
		$redis = self::getredis();
		try{
			$all = $redis->hGetAll(Config::$live_heart);
			return $all?$all:[];
		}catch (Exception $e){
			return [];
		}
	}

	/*public static function getlivesbyuids($uid = []){
		$lives = [];
		if(empty($uid)||!is_array($uid))
			return $lives;
		$db = self::getInstance();
		$bdparams = $db->buildInPrepare($uid);
		$sql = "SELECT * FROM (SELECT * FROM `live` WHERE `uid` IN (".$bdparams.")"
			." ORDER BY `liveid` DESC) live  GROUP BY uid ORDER BY liveid DESC";
		try{

		}catch (Exception $e){

		}
	}*/
	/**
	 * select live by liveids
	 * @param array $liveids
	 * @param array $field
	 * @param bool  $mater true select live |false select slavelive
	 *
	 * @return array
	 */
	public static function getlivebyid($liveids = [],$field = [],$mater = false){
		$lives = [];
		if(!is_array($liveids)||!is_array($field))
			return $lives;
		if(!in_array('liveid',$field))
			$field[] = 'liveid';
		$sqlfield = implode(',',$field);
		$db = self::getInstance();
		$bdparams = $db->buildInPrepare($liveids);
		if($mater){
			$sql = "SELECT {$sqlfield} FROM `live` WHERE `liveid` IN({$bdparams})";
		}else{
			$sql = "SELECT {$sqlfield} FROM `slavelive` WHERE `liveid` IN({$bdparams}) ORDER BY `slaveid` ASC";
		}
		try{
			$results = $db->query($sql, $liveids);
		}catch (Exception $e){
			throw $e;
		}
		if(empty($results))
			return $lives;
		foreach ($results as $result){
			if(empty($result))
				continue;
			$lives[$result['liveid']] = $result;
		}
		return $lives;
	}

	public static function getlivebystatus($status = [],$field = [],$mater = false){
		$lives = [];
		if(!is_array($status)||!is_array($field))
			return $lives;
		$sqlfield = implode(',',$field);
		$db = self::getInstance();
		$bdparams = $db->buildInPrepare($status);
		if($mater){
			$sql = "SELECT {$sqlfield} FROM `live` WHERE `status` IN({$bdparams})";
		}else{
			$sql = "SELECT {$sqlfield} FROM `slavelive` WHERE `status` IN({$bdparams}) ORDER BY `slaveid` ASC";
		}
		try{
			$results = $db->query($sql, $status);
		}catch (Exception $e){
			throw $e;
		}
		if(empty($results))
			return $lives;
		foreach ($results as $result){
			if(empty($result))
				continue;
			$lives[$result['liveid']] = $result;
		}
		return $lives;
	}

	public static function getheartlive($status = [],$field = [],$master = true){
		$db = self::getInstance();
		$bdparams = $db->buildInPrepare($status);
		$sqlfield = $db->buildFieldsParam($field);
		$table = $master?self::$master_live_table:self::$slave_live_table;
		$sql = "SELECT {$sqlfield} FROM {$table} WHERE status IN({$bdparams})";
		try{
			$results = $db->query($sql, $status);
		}catch (Exception $e){
			throw $e;
		}
		return $results;
	}

	public static function getheartstream($streams = [],$field = [],$master = true){
		$db = self::getInstance();
		$bdparams = $db->buildInPrepare($streams);
		$sqlfield = $db->buildFieldsParam($field);
		$table = $master?self::$master_stream_table:self::$slave_stream_table;
		$sql = "SELECT {$sqlfield} FROM {$table} WHERE `stream` IN({$bdparams})";
		try{
			$results = $db->query($sql, $streams);
		}catch (Exception $e){
			throw $e;
		}
		return $results;
	}
}

