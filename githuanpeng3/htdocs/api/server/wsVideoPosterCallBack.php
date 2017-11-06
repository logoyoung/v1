<?php

/**
 * video poster callback
 * v 2.0
 * date 2017-09-12
 */

include '../../../include/init.php';

use lib\live\LiveHelper;
use lib\live\VideoHelper;
use lib\live\LiveLog;
use lib\live\Config;

class wsPoster{
	protected $params = [];
	protected $master = null;

	public function getparams(){
		$content = @file_get_contents( 'php://input' );
		if(!$content)
			return false;
		$content = json_decode( base64_decode( $content ), true );
		if(empty($content['id'])||empty($content['items'][0]['key'])||empty($content['inputkey']))
			return false;
		$this->params['taskid'] = $content['id'];
		$key = basename($content['items'][0]['key']);
		$this->params['poster'] = $key;
		$this->params['vfile']  = $content['inputkey'];
		$key = explode('-',$key);
		if(!isset($key[0])||!isset($key[1])||!in_array($key[0],[LiveHelper::$master_stream_prefix,
				LiveHelper::$slave_stream_prefix]))
			return false;
		$liveid = explode('.',$key[1]);
		if(!isset($liveid[0])||!is_numeric($liveid[0]))
			return false;
		$this->params['liveid'] = $liveid[0];
		$this->master = ($key[0] == LiveHelper::$master_stream_prefix)?true:false;
		return true;
	}
	public function main(){
		if(!$this->getparams()){
			LiveLog::wslog("video poster callback {$this->params['poster']} params failed");
			echo 1; exit;
		}
		//update merge
		VideoHelper::setmerge(['taskid'=>$this->params['taskid']],['status'=>Config::$v_opt['complete']]);
		//update live
		$livedata = ['status'=>LIVE_COMPLETE,'utime'=>date("Y-m-d H:i:s")];
		$livetable = $this->master?LiveHelper::$master_live_table:LiveHelper::$slave_live_table;
		LiveHelper::update(['liveid'=>$this->params['liveid']],$livedata,$livetable);
		//update video
		$r = VideoHelper::setvideopic($this->params['liveid'],$this->params['poster'],$this->master);
		//send msg
		VideoHelper::sendmsg($this->params['liveid']);
		echo 1;exit;
	}
}

$v = new wsPoster();
$v->main();