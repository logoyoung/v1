<?php

/**
 *
 * merge callback
 * v 2.0
 * date 2017-09-12
 */

include '../../../include/init.php';

use lib\live\LiveHelper;
use lib\live\VideoHelper;
use lib\live\Config;
use lib\live\LiveLog;
class wsMerge{

	protected $params = [];
	protected $master = null;
	public function getparams(){
		$content = @file_get_contents( 'php://input' );
		if(!$content)
			return false;
		$content = json_decode( base64_decode( $content ), true );
		if(empty($content['id']))
			return false;
		$this->params['taskid'] = $content['id'];
		$key = basename($content['items'][0]['key']);
		$vname = $key;
		$key = explode('-',$key);
		if(!isset($key[0])||!isset($key[1])||!in_array($key[0],[LiveHelper::$master_stream_prefix,
				LiveHelper::$slave_stream_prefix]))
			return false;
		$liveid = explode('.',$key[1]);
		if(!isset($liveid[0])||!is_numeric($liveid[0]))
			return false;
		$this->params['liveid'] = $liveid[0];
		$this->master = ($key[0] == LiveHelper::$master_stream_prefix)?true:false;
		$this->params['duration'] = (int)$content['items'][0]['duration'];
		$this->params['offset']  = (int)($this->params['duration']/2);
		$this->params['vname']	= Config::$v_dir[$GLOBALS['env']]['v'] . $vname;
		$table = $this->master?LiveHelper::$master_live_table:LiveHelper::$slave_live_table;
		$live = LiveHelper::getlivebykey(['liveid'=>$this->params['liveid']],['orientation'],$table);
		if(empty($live[0]))
			return false;
		$live = $live[0];
		if($live['orientation'] == Config::$live_orientation['vertical'])
			$this->params['cut'] = "o/".$this->params['offset']."/w/".Config::$p_s['h']."/h/".Config::$p_s['w'];
		else
			$this->params['cut'] = "o/".$this->params['offset']."/w/".Config::$p_s['w']."/h/".Config::$p_s['h'];
		$this->params['poster'] = $key[0] . "-" . $this->params['liveid'] . "." . Config::$video_ext['pic'];
		return true;
	}

	public function main(){
		if(!$this->getparams()){
			LiveLog::wslog("stream {$this->params['vname']} params incorrect");
			echo 1;exit;
		}
		//update merge
		$updata = ['status'=>Config::$v_opt['complete'],'length'=>$this->params['duration']];
		VideoHelper::setmerge(['taskid'=>$this->params['taskid']],$updata);
		//update live
		$livedata = ['status'=>FLV_TO_VIDEO,'utime'=>date("Y-m-d H:i:s")];
		$livetable = $this->master?LiveHelper::$master_live_table:LiveHelper::$slave_live_table;
		LiveHelper::update(['liveid'=>$this->params['liveid']],$livedata,$livetable);
		//cut pic
		$ret = VideoHelper::cutpic($this->params['vname'],$this->params['poster'],$this->params['cut']);
		$ret = json_decode( $ret, true );
		if( !isset( $ret['persistentId'] ) ){
			LiveLog::wslog("cut video poster {$this->params['poster']} failed");
			echo 1;exit;
		}
		//add merge
		$data = [
			'taskid' => $ret['persistentId'],
			'liveid' => $this->params['liveid'],
			'opt'    => Config::$v_opt['poster'],
			'bucket' => Config::$v_bucket,
			'vname'  => $this->params['poster'],
		];
		VideoHelper::addmerge($data);
		//add video  length poster vfile liveid utime
		$livefield = [
			'uid',
			'gametid',
			'gameid',
			'gamename',
			'title',
			'ip',
			'port',
			'orientation',
			'stop_reason',
			'livetype',
		];
		$live = LiveHelper::getlivebykey(['liveid'=>$this->params['liveid']],$livefield,LiveHelper::$master_live_table);
		$live = $live[0];
		if(!$this->master){
			$live['orientation'] = Config::$live_orientation['vertical'];
			$live['livetype']	 = Config::$live_type['doubelslave'];
		}
		$live['length'] = $this->params['duration'];
		$live['vfile'] = $this->params['vname'];
		$live['liveid'] = $this->params['liveid'];
		$live['utime'] = date('Y-m-d H:i:s');
		VideoHelper::addvideo($live);
		echo 1;exit;
	}
}

$v = new wsMerge();
$v->main();