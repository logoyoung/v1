<?php

/**
 *
 * get flv or mp4
 * v 2.0
 * date 2017-09-11
 *
 */

include '../../../include/init.php';
use lib\live\LiveHelper;
use lib\live\LiveLog;
use lib\live\VideoHelper;
use lib\live\Config;

class videoCallBack{

	protected $params = [];
	protected $master = null;
	protected $flvs = [];

	public function getparams(){
		$content = @file_get_contents( 'php://input' );
		if(!$content)
			return false;
		$content = json_decode( base64_decode( $content ), true );
		if(!isset($content['items'][0]))
			return false;
		$data = $content['items'][0];
		$stream  = explode( '.', $data['streamname'] );
		$stream  = str_replace( 'liverecord-', '', $stream[0] );
		$prefix  = explode('-',$stream);
		if(empty($prefix[0])||!in_array($prefix[0],[LiveHelper::$master_stream_prefix,LiveHelper::$slave_stream_prefix]))
			return false;
		if(empty($prefix[1]) || !is_numeric($prefix[1]))
			return false;
		$this->params['liveid'] = $prefix[1];
		$this->master = ($prefix[0] == LiveHelper::$master_stream_prefix)?true:false;
		$this->params['stream'] = $stream;
		$this->params['bucket'] = $data['bucket'];
		$key = explode( ':', $data['keys'][0] );
		$key = isset( $key[1] ) ? $key[1] : $key[0];
		$this->params['key'] = $key;
		$this->params['url'] = '';
		$this->params['length'] = (int)$data['detail'][0]['duration'];
		return $this->params = xss_clean($this->params);
	}
	public function checkmerge(){
		//check live stop
		//$livetable = $this->master?LiveHelper::$master_live_table:LiveHelper::$slave_live_table;
		$live = LiveHelper::getlivebykey(['liveid'=>$this->params['liveid']],['status'],LiveHelper::$master_live_table);
		if(!isset($live[0]['status'])||$live[0]['status']!=LIVE_STOP)
			return false;
		//check all flv back
		$streams = VideoHelper::getstreams($this->params['liveid'],$this->master);
		$record = VideoHelper::getflvs($this->params['liveid'],$this->master);
		$keys = $record['keys'];
		$flvs = $record['stream'];
		$keys[] = $this->params['key'];
		$this->flvs = $keys;
		$flvs[] = $this->params['stream'];
		$diff = array_diff($streams,$flvs);
		if(!empty($diff))
			return false;
		return true;
	}
	public function addflv(){
		$data = [
			'liveid' => $this->params['liveid'],
			'stream' => $this->params['stream'],
			'bucket' => $this->params['bucket'],
			'keys'   => $this->params['key'],
			'urls'	 => $this->params['url'],
			'length' => $this->params['length'],
		];
		return VideoHelper::addflv($data,$this->master);
	}
	public function merge(){
		if(empty($this->flvs)){
			LiveLog::wslog("can not merge empty flv");
			return false;
		}
		if(count($this->flvs) == 1 && pathinfo($this->flvs[0],PATHINFO_EXTENSION) == Config::$video_ext['dest']){
			$this->move();
			$opt = Config::$v_opt['move'];
		}
		else{
			$opt = count($this->flvs)>1?Config::$v_opt['merge']:Config::$v_opt['transcode'];
			if($this->master)
				$save = LiveHelper::$master_stream_prefix . "-" . $this->params['liveid'] . '.' . Config::$video_ext['dest'];
			else
				$save = LiveHelper::$slave_stream_prefix . "-" . $this->params['liveid'] . '.' . Config::$video_ext['dest'];
			$ret = VideoHelper::mergefiles($this->flvs,$save);
			$ret = json_decode( $ret, true );
			if( !isset( $ret['persistentId'] ) ){
				LiveLog::wslog("merge {$save} failed " . json_encode($ret));
				return false;
			}
			$livetable = $this->master?LiveHelper::$master_live_table:LiveHelper::$slave_live_table;
			$livedata = ['status'=>LIVE_TO_FLV,'utime'=>date("Y-m-d H:i:s")];
			LiveHelper::update(['liveid'=>$this->params['liveid']],$livedata,$livetable);
			$mergerecord = [
				'taskid' => $ret['persistentId'],
				'liveid' => $this->params['liveid'],
				'opt'    => $opt,
				'bucket' => $this->params['bucket'],
				'vname'  => Config::$v_dir[$GLOBALS['env']]['v'] . $save,
			];
			return VideoHelper::addmerge($mergerecord);
		}

	}
	public function move(){

	}
	public function main(){
		$params = $this->getparams();
		if(!$params){
			LiveLog::wslog("stream {$this->params['stream']} params incorrect");
			echo 1;exit;
		}
		if(!$this->checkmerge()){
			$this->addflv();
		}else{
			$this->addflv();
			$this->merge();
		}
		echo 1;exit;
	}
}

$v = new videoCallBack();
$v->main();