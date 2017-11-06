<?php
/**
 * video timeout
 * v 1.0
 * date 2017-09-15
 */

include( __DIR__ . '/../../include/init.php' );

use lib\live\LiveHelper;
use lib\live\VideoHelper;
use lib\live\Config;
use lib\live\LiveLog;

class videoheart{

	protected $timeout = 3600;
	protected $master  = null;

	protected $lives = [];

	public function __construct($master = true){
		$this->master = $master;
	}
	public function getstask(){
		$status = [
			LIVE_STOP,
			LIVE_TO_FLV,
			FLV_TO_VIDEO,
		];
		$field = [
			'liveid',
			'status',
			'utime',
		];
		$table = $this->master?LiveHelper::$master_live_table:LiveHelper::$slave_live_table;
		$lives = LiveHelper::getlivebystatus($status,$field,$table);
		foreach ($lives as $live){
			$this->lives[$live['status']][] = $live;
		}
		//var_dump($this->lives);
		return true;
	}
	public function flvtimeout(){
		if(!isset($this->lives[LIVE_STOP]))
			return true;
		foreach ($this->lives[LIVE_STOP] as $live){
			$dtime = time()-strtotime($live['utime']);
			if($dtime < $this->timeout)
				continue;
			//get check master
			if(!$this->master && !$this->checkmaster($live['liveid']))
				continue;
			//get flv
			$flvs = VideoHelper::getflvs($live['liveid'],$this->master);
			if(empty($flvs['keys']))
				continue;
			LiveLog::videoheart(json_encode($live));
			$this->flvs = $flvs['keys'];
			$opt = count($this->flvs)>1?Config::$v_opt['merge']:Config::$v_opt['transcode'];
			if($this->master)
				$save = LiveHelper::$master_stream_prefix . "-" . $live['liveid'] . '.' . Config::$video_ext['dest'];
			else
				$save = LiveHelper::$slave_stream_prefix . "-" . $live['liveid'] . '.' . Config::$video_ext['dest'];
			$ret = VideoHelper::mergefiles($this->flvs,$save);
			$ret = json_decode( $ret, true );
			LiveLog::videoheart('merge video as '.$save);
			if( !isset( $ret['persistentId'] ) ){
				LiveLog::videoheart('merge error' . json_encode($ret));
				continue;
			}
			$livetable = $this->master?LiveHelper::$master_live_table:LiveHelper::$slave_live_table;
			$livedata = ['status'=>LIVE_TO_FLV,'utime'=>date("Y-m-d H:i:s")];
			LiveHelper::update(['liveid'=>$live['liveid']],$livedata,$livetable);
			$mergerecord = [
				'taskid' => $ret['persistentId'],
				'liveid' => $live['liveid'],
				'opt'    => $opt,
				'bucket' => Config::$v_bucket,
				'vname'  => Config::$v_dir[$GLOBALS['env']]['v'] . $save,
			];
			VideoHelper::addmerge($mergerecord);
		}
	}

	public function videotimeout(){
		if(!isset($this->lives[LIVE_TO_FLV]))
			return true;
		foreach ($this->lives[LIVE_TO_FLV] as $live){
			$dtime = time()-strtotime($live['utime']);
			if($dtime < $this->timeout)
				continue;
			$data = [
				'status'=>VIDEO_MERGE_FAILED,
				'utime' =>date('Y-m-d H:i:s'),
			];
			$table = $this->master?LiveHelper::$master_live_table:LiveHelper::$slave_live_table;
			LiveLog::videoheart(json_encode($live));
			LiveHelper::update(['liveid'=>$live['liveid']],$data,$table);
		}
	}

	public function postertimeout(){
		if(!isset($this->lives[FLV_TO_VIDEO]))
			return true;
		foreach ($this->lives[FLV_TO_VIDEO] as $live){
			$dtime = time()-strtotime($live['utime']);
			if($dtime < $this->timeout)
				continue;
			$data = [
				'status'=>VIDEO_TO_POSTER_FAILED,
				'utime' =>date('Y-m-d H:i:s'),
			];
			$table = $this->master?LiveHelper::$master_live_table:LiveHelper::$slave_live_table;
			LiveLog::videoheart(json_encode($live));
			LiveHelper::update(['liveid'=>$live['liveid']],$data,$table);
		}
	}
	public function checkmaster($liveid){
		$liveid = LiveHelper::getlivebykey(['liveid'=>$liveid],['status'],LiveHelper::$master_live_table);
		if(isset($liveid[0]['status']) && $liveid[0]['status']>=LIVE_STOP)
			return true;
		return false;
	}

	public function main(){
		$this->getstask();
		$this->flvtimeout();
		$this->videotimeout();
		$this->postertimeout();
	}
}

$master = isset($argv[1])?$argv[1]:'';
$master = ($master == 'slave')?false:true;
$videoheart = new videoheart($master);
$videoheart->main();