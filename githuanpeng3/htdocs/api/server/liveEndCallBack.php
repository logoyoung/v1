<?php
/**
 * stream callback
 * v 2.0
 * date 2017-09-08
 */


include '../../../include/init.php';
use lib\Anchor;
use lib\CDNHelper;
use LiveRoom;
use lib\live\LiveHelper;
use lib\live\LiveLog;

class liveEndCallBack{

	protected $params = [];
	protected $type = [
		'startlive' => 'cdn/ws/livestart',
		'disconnect'	=> 'cdn/ws/liveend',
	];
	protected $master = null;

	public function getparams($params = []){
		if(empty($params))
			return false;
		$params = array_change_key_case($params,CASE_LOWER);
		if(empty($params['ip']) || !is_string($params['ip']))
			return false;
		$this->params['ip'] = $params['ip'];
		if(empty($params['stream']) || !is_string($params['stream']))
			return false;
		$this->params['stream'] = $params['stream'];
		if(empty($params['node']) || !is_string($params['node']))
			return false;
		$this->params['node'] = $params['node'];
		if(empty($params['domain']) || !is_string($params['domain']))
			return false;
		$this->params['domain'] = $params['domain'];
		if(empty($params['path']) || !is_string($params['path']))
			return false;
		$this->params['path'] = $params['path'];
		if(empty($params['tm']) || !is_string($params['tm']))
			return false;
		$this->params['tm'] = $params['tm'];
		if(empty($params['sign']) || !is_string($params['sign']))
			return false;
		$this->params['sign'] = $params['sign'];

		$uri = $_SERVER['REQUEST_URI'];
		if(strstr( $uri, $this->type['startlive'] )){
			LiveLog::wslog("stream {$this->params['stream']} start at time {$this->params['tm']}");
			$this->params['excute'] = 'startlive';
		}
		elseif(strstr( $uri, $this->type['disconnect'] )){
			LiveLog::wslog("stream {$this->params['stream']} stop at time {$this->params['tm']}");
			$this->params['excute'] = 'disconnect';
		}
		else
			return false;
		return $this->params = xss_clean($this->params);
	}

	public function check(){
		//token check
		$data = [
			'stream' => $this->params['stream'],
			'ip'	 => $this->params['ip'],
			'tm'	 => $this->params['tm'],
		];
		if( CDNHelper::getStreamCallBackSecret($data) != $this->params['sign'] )
			return false;
		$streamprefix = explode("-",$this->params['stream']);
		if(!isset($streamprefix[0]) || !in_array($streamprefix[0],
				[LiveHelper::$slave_stream_prefix,LiveHelper::$master_stream_prefix]))
			return false;
		$streamprefix = $streamprefix[0];
		$this->master = ($streamprefix == LiveHelper::$master_stream_prefix)?true:false;
		$live = LiveHelper::getlivebystream($this->params['stream'],['uid'],$this->master);
		if( empty($live) || !is_numeric($live['uid']) )
			return false;
		$this->params['uid'] = $live['uid'];
		return true;
	}

	public function main(){
		//get params

		if(!$this->getparams($_GET)){
			LiveLog::wslog("stream {$this->params['stream']} params incorrect");
			echo 0;exit;
		}
		if(!$this->check()){
			LiveLog::wslog("stream {$this->params['stream']} check failed");
			echo 0;exit;
		}

		$LiveHelper = new LiveHelper($this->params['uid'],$this->master);
		if($this->params['excute'] == 'startlive')
			$LiveHelper->startlive($this->params['tm']);
		elseif($this->params['excute'] == 'disconnect')
			$LiveHelper->disconnect($this->params['tm']);
		//$LiveHelper->$this->params['excute']($this->params['tm']);
		echo 1;
		exit;
	}

}

$liveEnd = new liveEndCallBack();
$liveEnd->main();