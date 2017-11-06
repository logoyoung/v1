<?php

/**
 *
 * live poster
 * v 2.0
 * date 207-09-08
 *
 */

include( '../../../include/init.php' );

use lib\live\LiveHelper;
use lib\live\LiveLog;
use service\event\EventManager;
use service\user\UserDataService;
use service\room\RoomManagerService;
use service\anchor\AnchorDataService;
use service\room\LiveRoomService;
use service\live\LiveService;

class liveposter{

	protected  $live = [];
	protected  $master = null;


	public function getparams(){
		$posters = @file_get_contents("php://input");
		if(!$posters)
			return false;
		$posters = json_decode(base64_decode($posters),true);
		if(!isset($posters['items']))
			return false;
		foreach ($posters['items'] as $k => $item){
			if(!isset($item['streamname']))
				continue;
			$streamname = explode(".",$item['streamname']);
			if(!isset($streamname[0]))
				continue;
			$streamname = $streamname[0];
			$streamname = explode("-",$streamname);
			if(empty($streamname[1]) || empty($streamname[2]) || empty($streamname[3]))
				continue;
			if($streamname[1] == LiveHelper::$master_stream_prefix)
				$this->master = true;
			elseif($streamname[1] == LiveHelper::$slave_stream_prefix)
				$this->master = false;
			else
				continue;
			$streamname = "{$streamname[1]}-{$streamname[2]}-{$streamname[3]}";
			$live  = LiveHelper::getlivebystream($streamname,[],$this->master);
			if(empty($live))
				continue;
			if(!strstr($item['keys'][0],\lib\live\Config::$poster_type['big'][$live['orientation']]))
				continue;
			$live['poster'] = basename($item['urls'][0]) . "?" . time();
			$this->live[] = $live;
		}
		return true;
	}

	public function main(){
		$params = $this->getparams();
		if(!$params){
			echo 0;exit;
		}
		$liveService = new LiveService();
                $liveService->setCaller('API:' . __FILE__);
                
		foreach ($this->live as $live){
			$liveService->updateLivePosterRedis( $live['liveid'], $live['poster'], $this->master);
			$table = $this->master?LiveHelper::$master_live_table:LiveHelper::$slave_live_table;
			LiveHelper::update(['liveid'=>$live['liveid']],['poster'=>$live['poster']], $table);
			LiveLog::wslog("update {$live['poster']}");
		}
		echo 1;exit;
	}
}

$liveposter = new liveposter();
$liveposter->main();