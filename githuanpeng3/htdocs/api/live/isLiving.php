<?php
/**
 * check the user is living
 * when you publish a live you should
 * get this api to be sure that you ara
 * not living.
 * v 2.0
 * date 2017-09-04
 */
include '../../../include/init.php';

use lib\Anchor;
use lib\live\LiveHelper;
use service\user\UserAuthService;
use lib\live\LiveLog;
use lib\live\Config;

class isLiving{

	protected $params = [
		'uid' => 0,
		'encpass' => '',
		'deviceid' => '',
	];

	protected $liveinfo = [
		'gameID'=>'',
		'gameTypeID'=>'',
		'gameName'=>'',
		'liveType'=>'',
		'title'=>"",
		'orientation'=>1,
		'quality'=>''
	];

	protected $ajaxreturn = [
		'status' => 1,
		'liveID' => 0,
		'isdoubel' => 0,
		'list'   => [],
		'appQualityConf' => [],
		'pcQualityConf' =>  [],
	];

	/**
	 * get params and check it
	 * @param array $params
	 *
	 * @return array|bool|mixed|string
	 */
	public function getParams($params = []){
		if(empty($params))
			return false;
		$params = array_change_key_case($params,CASE_LOWER);
		if(empty($params['uid']) || !is_numeric($params['uid']))
			return false;
		$this->params['uid'] = $params['uid'];
		if(empty($params['encpass']) || !is_string($params['encpass']))
			return false;
		$this->params['encpass'] = $params['encpass'];
		if(empty($params['deviceid']))
			return false;
		$this->params['deviceid'] = $params['deviceid'];
		return $this->params = xss_clean($this->params);
	}

	/**
	 * auth check
	 * @return int errorcode
	 */
	public function authCheck(){
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

	public function getAjaxData(){
		// init ajaxreturn
		$this->ajaxreturn['status'] = Config::$live_pub_type['new'];
		$this->ajaxreturn['list'] = $this->liveinfo;
		$this->ajaxreturn['appQualityConf'] = Config::$app_quality;
		$this->ajaxreturn['pcQualityConf']  = Config::$pc_quality;
		$LiveHelper = new LiveHelper($this->params['uid']);
		$live = $LiveHelper->getReccentLive($this->params['uid']);
		//first live
		if(empty($live))
			return $this->ajaxreturn;

		$this->format($live);
		$this->ajaxreturn['list'] = $this->liveinfo;
		//new live
		if( isset( $live['status'] ) && $live['status'] != LIVE && $live['status'] != LIVE_CREATE ){
			return $this->ajaxreturn;
		}

		$this->ajaxreturn['liveID'] = $live['liveid'];
		//doubel live
		if(isset($live['livetype']) && $live['livetype'] == Config::$live_type['doubelmaster']
			&& isset($live['deviceid']) && $live['deviceid'] != $this->params['deviceid']
		){
			$slavelive = $LiveHelper->getReccentLive($this->params['uid'],true, LiveHelper::$slave_live_table);
			$this->ajaxreturn['appQualityConf'] = Config::$doubel_quality;
			$this->ajaxreturn['status'] = Config::$live_pub_type['new'];
			$this->ajaxreturn['list']['liveType'] = Config::$live_type['doubelslave'];
			if(empty($slavelive))
				return $this->ajaxreturn;
			$this->format($slavelive);
			$this->ajaxreturn['list'] = $this->liveinfo;
			if($this->params['deviceid'] == $slavelive['deviceid'])
				$this->ajaxreturn['status'] = Config::$live_pub_type['continue'];
			else
				$this->ajaxreturn['status'] = Config::$live_pub_type['otherdevice'];
			return $this->ajaxreturn;
		}
		//continue live
		else{
			if($this->params['deviceid'] == $live['deviceid'])
				$this->ajaxreturn['status'] = Config::$live_pub_type['continue'];
			else
				$this->ajaxreturn['status'] = Config::$live_pub_type['otherdevice'];
			return $this->ajaxreturn;
		}
	}

	public function format($live){
		$this->liveinfo['gameID']      = $live['gameid'];
		$this->liveinfo['gameTypeID']  = $live['gametid'];
		$this->liveinfo['gameName']    = $live['gamename'];
		$this->liveinfo['liveType']    = $live['livetype'];
		$this->liveinfo['title']       = $live['title'] ? $live['title'] : '';
		$this->liveinfo['orientation'] = $live['orientation'] ? $live['orientation'] : '0';
		$this->liveinfo['quality']     = $live['quality'] ? $live['quality'] : '0';
	}

	public function excute(){
		//get params
		$params = $this->getParams($_POST);
		if(!$params)
			error2(Config::$error_code['auth_params']);
		//auth check
		$authck = $this->authCheck();
		if($authck)
			error2($authck,2);
		//get ajax data
		$this->getAjaxData();
		LiveLog::applog("the user {$this->params['uid']} will start a live ");
		succ($this->ajaxreturn);
	}

}

//main
$isLiving = new isLiving();
$isLiving->excute();


