<?php
/**
 * continue live
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

class continueLiving{

	protected $params = [];

	public function getparams($params = []){
		if(empty($params))
			return false;
		$params = array_change_key_case($params,CASE_LOWER);

		$this->params['quality'] = isset($params['quality'])?trim($params['quality']):0;
		$this->params['orientation'] = isset($params['orientation'])?trim($params['orientation']):0;
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
		if(empty($params['liveid']) || !is_numeric($params['liveid']))
			return false;
		$this->params['liveid'] = $params['liveid'];
		if(!isset($params['livetype']) || !is_numeric($params['livetype']))
			return false;
		$this->params['livetype'] = $params['livetype'];
		if(empty($params['gamename']) || !is_string($params['gamename']))
			return false;
		$this->params['gamename'] = $params['gamename'];
		if(empty($params['title']) || !is_string($params['title']))
			return false;
		$this->params['title'] = $params['title'];
		return $this->params = xss_clean($this->params);
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
	public function continuelive(){
		$master = ($this->params['livetype'] == Config::$live_type['doubelslave']
			&& !empty($this->params['liveid']) )?false:true;
		$live = new LiveHelper($this->params['uid'], $master);
		$params = $this->params;
		unset($params['uid']);
		unset($params['encpass']);
		return $live->createlive($params);
	}
	public function main(){
		//get params
		$params = $this->getparams($_POST);
		if(!$params)
			error2(Config::$error_code['auth_params']);
		//auth check
		$authcode = $this->authcheck();
		if($authcode){
			//todo log
			error2($authcode,2);
		}
		//continue live
		$live = $this->continuelive();
		if(!isset($live['liveid'])){
			//todo log
			error2($live['code'],2);
		}
		$data = [
			'server'=>array($live['server']),
			'stream' => $live['stream'] . "?" . $live['token'],
			'hpbean' => 0,
			'fansCount' => 0,
			'userCount'=>0
		];
		succ($data);
	}
}

$continue = new continueLiving();
$continue->main();