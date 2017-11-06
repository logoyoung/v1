<?php
/**
 * stop live
 * v 2.0
 * date 2017-09-07
 *
 */

include '../../../include/init.php';
use lib\Anchor;
use lib\live\LiveHelper;
use service\user\UserAuthService;
use lib\live\LiveLog;
use lib\live\Config;

class stopLive{

	protected $params = [];

	public function getparams($params){
		if(empty($params))
			return false;
		$params = array_change_key_case($params,CASE_LOWER);
		if(!isset($params['uid'])||!is_numeric($params['uid']))
			return false;
		$this->params['uid'] = $params['uid'];
		if(!isset($params['encpass'])||!is_string($params['encpass']))
			return false;
		$this->params['encpass'] = $params['encpass'];
		if(!isset($params['liveid'])||!is_string($params['liveid']))
			return false;
		$this->params['liveid'] = $params['liveid'];
		if(isset($params['livetype'])&&is_numeric($params['livetype']))
			$this->params['livetype'] = $params['livetype'];
		return $this->params = xss_clean($this->params);
	}

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
	public function stop(){
		$master = (isset($this->params['livetype']) && $this->params['livetype']== Config::$live_type['doubelslave'])?false:true;
		$LiveHelper = new LiveHelper($this->params['uid'],$master);
		$LiveHelper->anchorstoplive();
	}

	public function main(){
		$params = $this->getparams($_POST);
		if(!$params)
			error2(Config::$error_code['auth_params']);
		//auth check
		$authck = $this->authcheck();
		if($authck)
			error2($authck,2);
		$this->stop();
		LiveLog::applog("the user {$this->params['uid']} stoped live {$this->params['liveid']}");
		succ();
	}
}

$stop = new stopLive();
$stop->main();