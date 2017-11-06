<?php
/**
 * check live heart
 * v 1.0
 * date 2017-09-13
 */

include( __DIR__ . '/../../include/init.php' );
use lib\live\LiveHelper;
use lib\live\VideoHelper;
use lib\live\LiveLog;

class liveheart{
	protected $disconnect_time = 180;
	protected $lives = [];
	protected $curtime = 0;
	protected $master = null;
	protected $livefield = [
		'liveid',
		'uid',
		'gamename',
		'livetype',
		'status',
		'utime',
		'stream',
	];
	protected $streamfield = [
		'liveid',
		'status',
		'utime',
		'stream',
	];
	public function __construct($master = true){
		$this->master = $master;
		$this->curtime = time();
	}

	public function main(){
		//get create lives
		$lives = LiveHelper::getheartlive([LIVE_CREATE,LIVE],$this->livefield,$this->master);
		if(empty($lives)){
			exit;
		}
		$streamkey = array_map(function($v){
			return $v['stream'];
		},$lives);
		$streamlist = LiveHelper::getheartstream($streamkey,$this->streamfield,$this->master);
		unset($streamkey);
		$streams = [];
		foreach ($streamlist as $stream){
			$streams[$stream['liveid']] = $stream;
		}
		unset($streamlist);

		foreach ($lives as $live){
			//check L0
			if($live['status'] == LIVE_CREATE){
				$dtime = $this->curtime - strtotime($live['utime']);
				if($dtime > $this->disconnect_time){
					LiveLog::liveheart(json_encode($live));
					LiveLog::liveheart(json_encode($streams[$live['liveid']]));
					LiveHelper::timeout($live,$this->master);
				}
			}
			else{
				//no stream
				if(!isset($streams[$live['liveid']])){
					LiveLog::liveheart(json_encode($live));
					LiveLog::liveheart(json_encode($streams[$live['liveid']]));
					LiveHelper::timeout($live,$this->master);
				}
				$dtime = $this->curtime - strtotime($streams[$live['liveid']]['utime']);
				$streamstatus = $streams[$live['liveid']]['status'];
				//check L100 S100

				if($streamstatus == STREAM_START){
					$streamname = $streams[$live['liveid']]['stream'];
					if(($dtime>\lib\live\Config::$heart_expire) && isset($streamname)
						&& ( !$this->checkheart($streamname)))
					{

						LiveHelper::timeout($live,$this->master);
					}
				}else{
					if($dtime > $this->disconnect_time){
						LiveLog::liveheart(json_encode($live));
						LiveLog::liveheart(json_encode($streams[$live['liveid']]));
						LiveHelper::timeout($live,$this->master);
					}
				}
			}
			continue;
		}

	}

	public function checkheart($stream){
		if(!\lib\live\Config::$heart_start)
			return true;
		if(empty($stream))
			return false;
		$time = LiveHelper::getstreamstatus($stream);
		if(empty($time))
			return false;
		/*if(time()-$time>\lib\live\Config::$heart_expire){
			LiveHelper::delstreamstatus($stream);
			return false;
		}*/
		return true;
	}
}

$master = isset($argv[1])?$argv[1]:'';
$master = ($master == 'slave')?false:true;
$liveheart = new liveheart($master);
$liveheart->main();