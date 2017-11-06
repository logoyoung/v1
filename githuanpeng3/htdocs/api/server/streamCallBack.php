<?php

/**
 *
 * stream auth
 * v 2.0
 * date 2017-09-08
 *
 *
 */

include( __DIR__ . '/../../../include/init.php' );
use lib\live\LiveHelper;
use lib\CDNHelper;
use lib\live\LiveLog;

class streamCallBack{

	protected $params = [];
	protected $master = null;

	public function getparams($params = []){
		if(empty($params))
			return false;
		$params = array_change_key_case($params,CASE_LOWER);
		if(empty($params['uid']) || !is_numeric($params['uid']))
			return false;
		$this->params['uid'] = $params['uid'];
		if(empty($params['liveid']) || !is_string($params['liveid']))
			return false;
		$this->params['liveid'] = $params['liveid'];
		if(empty($params['tm']) || !is_string($params['tm']))
			return false;
		$this->params['tm'] = $params['tm'];
		if(empty($params['sign']) || !is_string($params['sign']))
			return false;
		$this->params['sign'] = $params['sign'];
		return $this->params = xss_clean($this->params);
	}

	public function check(){
		$prefix = explode("-",$this->params['liveid']);
		if(!isset($prefix[0]) || !isset($prefix[1]) || !is_numeric($prefix[1]))
			return false;
		if($prefix[0] == LiveHelper::$master_stream_prefix)
			$this->master = true;
		elseif($prefix[0] == LiveHelper::$slave_stream_prefix)
			$this->master = false;
		else
			return false;
		$data = [
			'uid' => $this->params['uid'],
			'liveid' => $this->params['liveid'],
			'tm' => $this->params['tm'],
		];
		$token = CDNHelper::getPublishLiveSecret($data);
		if( $token != $this->params['sign'] || !LiveHelper::checkauthpubstream($prefix[1],$this->master) )
			return false;
		return true;
	}
	public function main(){
		if(!$this->getparams($_GET)){
			//todo log
			echo 0;exit;
		}
		if(!$this->check()){
			//todo log
			echo 0;exit;
		}
		echo 1;exit;
	}

}

$stream = new streamCallBack();
$stream->main();